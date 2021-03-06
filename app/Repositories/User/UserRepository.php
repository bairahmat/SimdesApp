<?php
namespace SimdesApp\Repositories\User;

use SimdesApp\Models\User;
use SimdesApp\Repositories\AbstractRepository;
use SimdesApp\Repositories\Contracts\UserInterface;
use SimdesApp\Services\LaraCacheInterface;
class UserRepository extends AbstractRepository implements UserInterface
{

    /**
     * @var LaraCacheInterface
     */
    protected $cache;

    /**
     * @param User               $user
     * @param LaraCacheInterface $cache
     */
    public function __construct(User $user, LaraCacheInterface $cache)
    {
        $this->model = $user;
        $this->cache = $cache;
    }

    /**
     * Instant find data User
     *
     * @param int  $page
     * @param int  $limit
     * @param null $term
     *
     * @return mixed
     */
    public function find($page = 1, $limit = 10, $term = null)
    {
        // Create Key for cache
        $key = 'user-find-' . $page . $limit . $term;
        // Create Section
        $section = 'user';
        // If cache is exist get data from cache
        if ($this->cache->has($section, $key)) {
            return $this->cache->get($section, $key);
        }
        // Search data
        $user = $this->model
            ->orderBy('level', 'asc')
            ->whereBetween('level', [1, 10])
            ->where('nama', 'like', '%' . $term . '%')
            ->paginate($limit)
            ->toArray();
        // Create cache
        $this->cache->put($section, $key, $user, 10);

        return $user;
    }

    /**
     * Create data User
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        try {
            $user = $this->getNew();
            // organisasi_id
            $organisasi_id = $data['organisasi_id'];
            // set the level user
            if (empty($organisasi_id)) {
                $level = $this->getLevel($data['level']);
            } else {
                $level = $data['level'];
            }
            $user->email = e($data['email']);
            $user->nama = e($data['nama']);
            $user->password = bcrypt($data['password']);
            $user->level = $level;
            $user->save();

            // Return result success
            return $this->successInsertResponse();
        } catch (\Exception $ex) {
            \Log::error('UserRepository create action something wrong -' . $ex);

            return $this->errorInsertResponse();
        }
    }

    /**
     * Find User by Id
     *
     * @param $id
     *
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Update data User
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($id, array $data)
    {
        try {
            $user = $this->findById($id);
            // organisasi_id
            $organisasi_id = $data['organisasi_id'];
            // set the level user
            if (empty($organisasi_id)) {
                $level = $this->getLevel($data['level']);
            } else {
                $level = $data['level'];
            }
            $user->email = e($data['email']);
            $user->nama = e($data['nama']);
            $user->is_active = e($data['is_active'], 1);
            $user->level = $level;
            $user->save();

            // Return result success
            return $this->successUpdateResponse();
        } catch (\Exception $ex) {
            \Log::error('UserRepository update action something wrong -' . $ex);

            return $this->errorUpdateResponse();
        }
    }

    /**
     * Delete data User
     *
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $user = $this->findById($id);
            if ($user) {
                $user->delete();

                return $this->successDeleteResponse();
            }

            return $this->emptyDeleteResponse();
        } catch (\Exception $ex) {
            \Log::error('UserRepository create action something wrong -' . $ex);

            return $this->errorDeleteResponse();
        }
    }

    /**
     * @param $level
     *
     * @return int
     */
    public function getLevel($level)
    {
        switch ($level) {
            // level Administrator
            case 1:
                return 210;
                break;
            // Kepala BPMD
            case 2:
                return 220;
                break;
            // Pimpinan / Bupati
            case 3:
                return 230;
                break;
            // SKPD-Adm. Pembangunan
            case 4:
                return 240;
                break;
            // SKPD-Adm. BAPEDA
            case 5:
                return 250;
                break;
            // SKPD BPMD
            case 6:
                return 260;
                break;
            // SKPD Fasilitator
            case 7:
                return 270;
                break;
            // SKPD HUKUM
            case 8:
                return 280;
                break;
            // SKPD Inspektorat
            case 9:
                return 290;
                break;
            // SKPD Keuangan Daerah
            case 10:
                return 300;
                break;
            // SKPD Urusan
            case 11:
                return 310;
                break;
            // SKPD Verifikasi
            case 12:
                return 320;
                break;
            default :
                return 1;
        }
    }

    /**
     * check organisasi for check before delete
     *
     * @param $organisasi_id
     *
     * @return mixed
     */
    public function findIsExists($organisasi_id)
    {
        return $this->model
            ->where('organisasi_id', '=', $organisasi_id)
            ->get();
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function resetPassword($email)
    {
        try {
            $user = $this->model->where('email', $email)->first();
            if ($user) {
                $rand = str_random(8);
                $user->password = \Hash::make($rand);
                $user->save();

                return $this->successResponseOk([
                    'success' => true,
                    'message' => [
                        'msg' => 'Password baru anda adalah ' . $rand,
                    ],
                ]);
            }

            return $this->successResponseOk([
                'success' => false,
                'message' => [
                    'msg' => 'Email yang anda masukkan tidak terdaftar.',
                ],
            ]);
        } catch (\Exception $ex) {
            \Log::error('UserRepository resetPassword action something wrong -' . $ex);

            return $this->errorUpdateResponse();
        }
    }

    /**
     * Get list data from sotfdelete
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getTrashed()
    {
        return $this->model->onlyTrashed()->get();
    }

    /**
     * Restore data from sotfdelete
     *
     * @return mixed
     */
    public function getRestore()
    {
        $this->model->withTrashed()->restore();

        return $this->successResponseOk([
            'success' => true,
            'message' => [
                'msg' => 'Sukses : Data berhasil direstore.',
            ],
        ]);
    }

    /**
     * Get a list User by Full Text Search Backoffice Filter
     *
     * @param null $term
     *
     * @return mixed
     */
    public function findByBackOffice($term)
    {
        return $this->model
            ->orderBy('level', 'asc')
            ->whereNotBetween('level', [1, 10])
            ->where('nama', 'like', '%' . $term . '%')
            ->paginate(10);
    }

    /**
     * Get a list User by Full Text Search Frontoffice Filter
     *
     * @param $term
     * @param $organisasi_id
     *
     * @return mixed
     */
    public function findByFrontOffice($term, $organisasi_id)
    {
        return $this->model
            ->orderBy('level', 'asc')
            ->where('organisasi_id', '=', $organisasi_id)
            ->whereBetween('level', [1, 20])
            ->where('nama', 'like', '%' . $term . '%')
            ->paginate(10);
    }

    /**
     * Set unActive User
     *
     * @param $id
     *
     * @return mixed
     */
    public function unActiveUser($id)
    {
        $user = $this->findById($id);
        $user->is_active = 0;
        $user->save();

        return $this->successResponseOk([
            'success' => true,
            'message' => [
                'msg' => 'Sukses : User berhasil dinonaktifkan.',
            ],
        ]);
    }

    /**
     * @param $id
     * @param $organisasi_id
     * @return mixed
     */
    public function findByOrganisasiId($id, $organisasi_id)
    {
        return $this->model->where('_id', '=', $id)->where('organisasi_id', '=', $organisasi_id)->first();
    }

    /**
     * Get the list user desa by organisasi_id is using in detil page
     *
     * @param $organisasi_id
     * @return mixed
     */
    public function listByOrganisasiId($organisasi_id)
    {
        // set key
        $key = 'user-list' . $organisasi_id;

        // set section
        $section = 'user';

        // has section and key
        if ($this->cache->has($section, $key)) {
            return $this->cache->get($section, $key);
        }

        // query to database
        $user = $this->model
            ->where('organisasi_id', '=', $organisasi_id)
            ->paginate(10)
            ->toArray();

        // store to cache
        $this->cache->put($section, $key, $user, 3600);

        return $user;
    }
}