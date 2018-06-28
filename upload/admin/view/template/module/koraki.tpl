<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-koraki" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
          <iframe src="https://koraki.io/opencart-intro-page/" style="border: 0;" height="200px" width="100%"></iframe>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-koraki" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="koraki_client_id"><?php echo $entry_client_id; ?></label>
            <div class="col-sm-10">
              <input class="form-control" type="text" name="koraki_client_id" id="koraki_client_id" value="<?php echo $koraki_client_id; ?>" placeholder="<?php echo $entry_client_id_placeholder; ?>" />
              <?php if ($error_client_id) { ?>
              <div class="text-danger"><?php echo $error_client_id; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="koraki_client_secret"><?php echo $entry_client_secret; ?></label>
            <div class="col-sm-10">
              <input class="form-control" type="text" name="koraki_client_secret" id="koraki_client_secret" value="<?php echo $koraki_client_secret; ?>" placeholder="<?php echo $entry_client_secret_placeholder; ?>" />
              <?php if ($error_client_secret) { ?>
              <div class="text-danger"><?php echo $error_client_secret; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="koraki_status" id="input-status" class="form-control">
                <?php if ($koraki_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>          
        </form>
      </div>
	</div>
  </div>
</div>

<?php echo $footer; ?>
