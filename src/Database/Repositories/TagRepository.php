<?php


namespace Xsimarket\Database\Repositories;


use Xsimarket\Database\Models\Tag;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;



class TagRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'        => 'like',
        'type.slug',
        'language'
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
        return Tag::class;
    }
}
