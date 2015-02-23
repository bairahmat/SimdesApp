<?php namespace SimdesApp\Repositories\Kewenangan;

use SimdesApp\Models\Kewenangan;
use SimdesApp\Repositories\AbstractRepository;
use SimdesApp\Services\LaraCacheInterface;

class KewenanganRepository extends AbstractRepository {

    /**
     * @var LaraCacheInterface
     */
    protected $cache;

    public function __construct(Kewenangan $kewenangan, LaraCacheInterface $cache)
    {
        $this->model = $kewenangan;
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
        $key = 'find-kewenangan-' . $page . $limit . $term;

        // Create Section
        $section = 'kewenangan';

        // If cache is exist get data from cache
        if ($this->cache->has($section, $key)) {
            return $this->cache->get($section, $key);
        }

        // Search data
        $kewenangan = $this->model
            ->where('kode_rekening', 'like', '%' . $term . '%')
            ->paginate($limit)
            ->toArray();

        // Create cache
        $this->cache->put($section, $key, $kewenangan, $limit);

        return $kewenangan;
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
            $kewenangan = $this->getNew();

            $kewenangan->kode_rekening = e($data['kode_rekening']);
            $kewenangan->kewenangan = e($data['kewenangan']);

            $kewenangan->save();

            /*Return result success*/
            return $this->successInsertResponse();

        } catch (\Exception $ex) {
            \Log::error('KewenanganRepository create action something wrong -' . $ex);
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
            $kewenangan = $this->findById($id);

            $kewenangan->kode_rekening = e($data['kode_rekening']);
            $kewenangan->kewenangan = e($data['kewenangan']);

            $kewenangan->save();

            /*Return result success*/
            return $this->successUpdateResponse();

        } catch (\Exception $ex) {
            \Log::error('KewenanganRepository update action something wrong -' . $ex);
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
            $kewenangan = $this->findById($id);

            if ($kewenangan){
                $kewenangan->delete();

                // Return result success
                return $this->successDeleteResponse();
            }

            return $this->emptyDeleteResponse();

        } catch (\Exception $ex) {
            \Log::error('KewenanganRepository destroy action something wrong -' . $ex);
            return $this->errorDeleteResponse();
        }
    }
}
