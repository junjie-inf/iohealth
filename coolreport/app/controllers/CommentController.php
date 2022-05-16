<?php

class CommentController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_REPORTS,report,report_id');
		$this->beforeFilter('permission:ADD_COMMENTS,report,report_id', array('only' => array('create', 'store')));
		$this->beforeFilter('permission:MODIFY_COMMENTS,comment', array('only' => array('edit', 'update', 'destroy')));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Report $report)
	{
		return $this->layout('pages.report.comment.index')
					->withData($report);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Report $report)
	{
		// Creamos el objeto
		$comment = new Comment;
		
		// Validamos y asignamos campos
		$v = $comment->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Asociamos relaciones externas
		$comment->report()->associate( $report );
		$comment->user()->associate( Auth::user() );

		// Guardamos el modelo
		if( $comment->save() )
		{
			Event::fire('comment.store', $comment);

			return $this->json( array(				
				'status' => 'OK', 
				'id' => $comment->id
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Report $report, Comment $comment)
	{
		$comment->delete();

		Event::fire('comment.destroy', $comment);

		return $this->json( array('status' => 'OK'), 200 );
	}

}