<?php

/**
 * Управление файлами документов.
 */
class WikiFileController extends Controller
{

    /**
     * Загрузка файла.
     */
    public function actionUpload()
    {

        $folder = Yii::getPathOfAlias('wikifiles') . DIRECTORY_SEPARATOR;
        $uploadedFile = CUploadedFile::getInstanceByName('upload');
        $uploadPath = SiteFile::createUploadPath($folder, $uploadedFile->name);
        $CKEditorFuncNum = Yii::app()->request->getQuery('CKEditorFuncNum');

        if ($uploadedFile->saveAs($uploadPath)) {
            $file = new WikiFile();

            $file->doc_id = Yii::app()->request->getQuery('doc_id');
            $file->path = $uploadPath;
            $file->original = $uploadedFile->name;
            $file->save();

            $uploadedImageURL = Yii::app()->createUrl('wiki/WikiFile/download', ['id' => $file->file_id]);
            $message = '';
        } else {
            $uploadedImageURL = '';
            $message = 'Ошибка при загрузке файла';
        }

        $this->render('ckeditor_message', [
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'message' => $message,
            'uploadedImageURL' => $uploadedImageURL
        ]);
    }

    /**
     * Скачивание файла
     */
    public function actionDownload($id)
    {

        $file = WikiFile::model()->findByPk($id);

        $path = Yii::getPathOfAlias('wikifiles') . DIRECTORY_SEPARATOR . $file->path;

        if (is_file($path)) {

            $file_extension = strtolower(substr(strrchr($file->path, "."), 1));

            switch ($file_extension) {
                case "gif":
                    $mtype = "image/gif";
                    break;
                case "png":
                    $mtype = "image/png";
                    break;
                case "jpeg":
                case "jpg":
                    $mtype = "image/jpeg";
                    break;
                default:
                    $mtype = "application/octet-stream";
                    break;
            }

            Yii::app()->request->sendFile($file->original, readfile($path), $mtype);
        } else {
            throw new CHttpException(404, 'Файл не найден.');
        }
    }

}
