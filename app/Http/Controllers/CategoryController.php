<?php

namespace Hydrofon\Http\Controllers;

use Hydrofon\Category;
use Hydrofon\Http\Requests\CategoryDestroyRequest;
use Hydrofon\Http\Requests\CategoryStoreRequest;
use Hydrofon\Http\Requests\CategoryUpdateRequest;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = QueryBuilder::for(Category::class)
                                  ->with(['parent'])
                                  ->leftJoin('categories as parent', 'parent.id', '=', 'categories.parent_id')
                                  ->allowedFilters('categories.name')
                                  ->allowedSorts(['categories.name', 'parent.name'])
                                  ->defaultSort('categories.name')
                                  ->select('categories.*')
                                  ->paginate(15);

        return view('categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Hydrofon\Http\Requests\CategoryStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->all());
        $category->groups()->sync($request->get('groups'));

        flash('Category was created');

        return redirect('/categories');
    }

    /**
     * Display the specified resource.
     *
     * @param \Hydrofon\Category $category
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('categories.show')->with('category', $category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Hydrofon\Category $category
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('categories.edit')->with('category', $category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Hydrofon\Http\Requests\CategoryUpdateRequest $request
     * @param \Hydrofon\Category                            $category
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update($request->all());
        $category->groups()->sync($request->get('groups'));

        flash('Category was updated');

        return redirect('/categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Hydrofon\Category                             $category
     * @param \Hydrofon\Http\Requests\CategoryDestroyRequest $request
     *
     * @return void
     */
    public function destroy(Category $category, CategoryDestroyRequest $request)
    {
        $category->delete();

        flash('Category was deleted');

        return redirect('/categories');
    }
}
