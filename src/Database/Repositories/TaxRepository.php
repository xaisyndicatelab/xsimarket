<?php


namespace Xsimarket\Database\Repositories;

use Xsimarket\Database\Models\Tax;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class TaxRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'        => 'like',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tax::class;
    }
}
