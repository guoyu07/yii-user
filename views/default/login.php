<?php $this->pageTitle = 'Anmeldung' ?>

<?php if($form->model->hasErrors()) Yii::app()->user->setFlash('error', 'Please fix the following input errors.'); ?>

<div class="form">
<?php echo $form->render(); ?>
</div>