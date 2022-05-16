<?php

class CreateAttachments {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attachments', function($table) 
		{
			$table->string('hash', 64);
			$table->primary('hash');
			
			$table->string('mimetype', 32);
			$table->integer('size');
			
			$table->timestamps();
			
			$table->softDeletes();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attachments');
	}

}