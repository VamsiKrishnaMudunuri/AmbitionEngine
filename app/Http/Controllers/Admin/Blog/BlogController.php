<?php

namespace App\Http\Controllers\Admin\Blog;

use Sess;
use Utility;
use Exception;
use SmartView;
use Translator;
use App\Models\Meta;
use App\Models\Blog;
use App\Models\Sandbox;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Blog());
    }

    public function index()
    {
        try {
            ${$this->plural()} = $this->getModel()->showAll();
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->plural(), 'sandbox'));
    }

    public function add()
    {
        try {
            ${$this->singular()} = $this->getModel();
            $sandbox = new Sandbox();
            $meta = new Meta();

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'sandbox', 'meta'));
    }

    public function postAdd(Request $request)
    {
        try {
            ${$this->singular()} = $this->getModel()->add(request()->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()
            ->route('admin::blog::index', array())
            ->with(Sess::getKey('success'), Translator::transSmart('app.Blog post has been added', 'Blog post has been added.'));
    }

    public function edit($id)
    {
        try {
            ${$this->singular()} = $this->getModel()->retrieve($id);
            $meta = ${$this->singular()}->metaWithQuery;
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'sandbox', 'meta'));
    }

    public function postEdit(Request $request, $id)
    {
        try {
            ${$this->singular()} = $this->getModel()->edit($id, request()->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::blog::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Blog post has been updated.', 'Blog post has been updated.'));

    }

    public function postPublish(Request $request, $id)
    {
        try {
            ${$this->singular()} = $this->getModel()->togglePublished($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }

    public function postDelete(Request $request, $id)
    {
        try {
            $this->getModel()->del($id);

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch (IntegrityException $e) {
            $this->throwIntegrityException(
                $request, $e
            );

        }  catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        } finally{
	
	        $request->flush();
	
        }

        return $this->responseIntended('admin::blog::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Blog post has been deleted.', 'Blog post has been deleted.'));

    }

}