<?php


namespace Xsimarket\GraphQL\Mutation;


use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Xsimarket\Facades\Shop;

class ShopMutator
{
    public function createShop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@store', $args);
    }
    public function updateSHop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@updateShop', $args);
    }
    public function deleteShop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@deleteShop', $args);
    }
    public function approveShop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@approveShop', $args);
    }
    public function disApproveShop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@disApproveShop', $args);
    }
    public function addStaff($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@addStaff', $args);
    }
    public function removeStaff($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@removeStaff', $args);
    }
    public function followShop($rootValue, array $args, GraphQLContext $context)
    {
        return Shop::call('Xsimarket\Http\Controllers\ShopController@handleFollowShop', $args);
    }
}
