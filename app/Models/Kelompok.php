<?php namespace SimdesApp\Models;


use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model {

    /**
     * @var string
     */
    protected $table = 'kelompok';

    /**
     * @var string
     */
    protected $with = 'akun';

    /**
     * @var array
     */
    protected $fillable = [
        'kode_rekening',
        'akun_id',
        'kelompok'
    ];

    /**
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'user_creator',
        'user_updater'
    ];

    /**
     * Boot the Model
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) {
            // flush the cache section
            \Cache::section('kelompok')->flush();
        });

        static::updating(function ($model) {
            // flush the cache section
            \Cache::section('kelompok')->flush();
        });

        static::deleting(function ($model) {
            // flush the cache section
            \Cache::section('kelompok')->flush();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function akun()
    {
        return $this->belongsTo('SimdesApp\Models\Akun', 'akun_id');
    }
}
