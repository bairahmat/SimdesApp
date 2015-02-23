<?php namespace SimdesApp\Repositories\RKPDES;

use SimdesApp\Models\Rkpdes;
use SimdesApp\Repositories\AbstractRepository;
use SimdesApp\Services\LaraCacheInterface;

class RkpdesRepository extends AbstractRepository {

    /**
     * @var LaraCacheInterface
     */
    protected $cache;

    public function __construct(Rkpdes $rkpdes, LaraCacheInterface $cache)
    {
        $this->model = $rkpdes;
        $this->cache = $cache;
    }

    /**
     * Instant find or search with paging, limit, and query
     *
     * @param int $page
     * @param int $limit
     * @param null $term
     * @return mixed
     */
    public function find($page = 1, $limit = 10, $term = null)
    {
        // Create Key for cache
        $key = 'find-rkpdes-' . $page . $limit . $term;

        // Create Section
        $section = 'rkpdes';

        // If cache is exist get data from cache
        if ($this->cache->has($section, $key)) {
            return $this->cache->get($section, $key);
        }

        // Search data
        $rkpdes = $this->model
            ->where('kegiatan', 'like', '%' . $term . '%')
            ->paginate($limit)
            ->toArray();

        // Create cache
        $this->cache->put($section, $key, $rkpdes, 10);

        return $rkpdes;
    }

    /**
     * Create data
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        try {
            $rkpdes = $this->getNew();

            $rkpdes->user_id = e($data['user_id']);
            $rkpdes->organisasi_id = e($data['organisasi_id']);
            $rkpdes->rpjmdes_program_id = e($data['rpjmdes_program_id']);
            $rkpdes->dana_desa_id = e($data['dana_desa_id']);
            $rkpdes->tahun = e($data['tahun']);
            $rkpdes->lokasi = e($data['lokasi']);
            $rkpdes->anggaran = $data['anggaran'];
            $rkpdes->kegiatan = e($data['kegiatan']);
            $rkpdes->pejabat_desa_id = e($data['pejabat_desa_id']);
            $rkpdes->is_finish = $data['is_finish'];

            $rkpdes->save();

            // Return result success
            return $this->successInsertResponse();

        } catch (\Exception $ex) {
            \Log::error('RkpdesRepository create action something wrong -' . $ex);
            return $this->errorInsertResponse();
        }
    }

    /**
     * Show the Record
     *
     * @param $id
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Update the record
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        try {
            $rkpdes = $this->findById($id);

            $rkpdes->user_id = e($data['user_id']);
            $rkpdes->organisasi_id = e($data['organisasi_id']);
            $rkpdes->rpjmdes_program_id = e($data['rpjmdes_program_id']);
            $rkpdes->dana_desa_id = e($data['dana_desa_id']);
            $rkpdes->tahun = e($data['tahun']);
            $rkpdes->lokasi = e($data['lokasi']);
            $rkpdes->anggaran = $data['anggaran'];
            $rkpdes->kegiatan = e($data['kegiatan']);
            $rkpdes->pejabat_desa_id = e($data['pejabat_desa_id']);
            $rkpdes->is_finish = $data['is_finish'];

            $rkpdes->save();

            // Return result success
            return $this->successUpdateResponse();

        } catch (\Exception $ex) {
            \Log::error('RkpdesRepository update action something wrong -' . $ex);
            return $this->errorUpdateResponse();
        }
    }

    /**
     * Destroy the record
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $rkpdes = $this->findById($id);

            if ($rkpdes){
                $rkpdes->delete();

                // Return result success
                return $this->successDeleteResponse();
            }

            return $this->emptyDeleteResponse();

        } catch (\Exception $ex) {
            \Log::error('RkpdesRepository destroy action something wrong -' . $ex);
            return $this->errorDeleteResponse();
        }
    }
}