<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
	/**
	 * @var UploadedFile file attribute
	 */
	public $file;

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			[['file'], 'file', 'maxFiles' => 10, 'on' => 'multiple'],
			[['file'], 'file', 'on' => 'single'],
			[['file'], 'file', 'extensions' => 'jpg, png, bmp, jpeg, gif', 'checkExtensionByMimeType' => false, 'on' => 'image'],
			[['file'], 'file', 'extensions' => 'mp3, wma, wav', 'checkExtensionByMimeType' => false, 'on' => 'music'],
			[['file'], 'file', 'extensions' => 'xls, xlsx', 'checkExtensionByMimeType' => false, 'on' => 'excel'],
			[['file'], 'file', 'extensions' => 'mp4, ogg, webm, wav, ogv, m4v', 'maxSize' => 1024*1024*50, 'checkExtensionByMimeType' => false, 'on' => 'video'],//, avi, wma, rmvb, rm, flash, mov, mkv, ts
		];
	}
	
}