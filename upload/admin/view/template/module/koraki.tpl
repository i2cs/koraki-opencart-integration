<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

                <table class="form">
			<tr><td colspan="3"><h3><?php echo $entry_credentials; ?></h3>
<p><?php echo $entry_credentials_help; ?></p></td></tr>
                    <tr>
                        <td><?php echo $entry_client_id; ?></td>
                        <td><input class="form-control" type="text" name="koraki_client_id" id="koraki_client_id" value="<?php echo $koraki_client_id; ?>" placeholder="<?php echo $entry_client_id_placeholder; ?>" /></td>
<td></td>
                    </tr>
		<tr>
                        <td><?php echo $entry_client_secret; ?></td>
                        <td><input class="form-control" type="text" name="koraki_client_secret" id="koraki_client_secret" value="<?php echo $koraki_client_secret; ?>" placeholder="<?php echo $entry_client_secret_placeholder; ?>" /></td>
<td></td>
                    </tr>



			<tr><td colspan="3"><h3><?php echo $entry_events; ?></h3></td></tr>


                    <tr>
                        <td><?php echo $entry_checkout; ?></td>
                        <td><select name="koraki_checkout" id="input-status" class="form-control">
                <?php if ($koraki_checkout) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
<td></td>
                    </tr>

<tr>
                        <td><?php echo $entry_registered; ?></td>
                        <td><select name="koraki_registered" id="input-status" class="form-control">
                <?php if ($koraki_registered) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td><td></td>
                    </tr>


		<tr><td colspan="3"><h3><?php echo $entry_widget; ?></h3></td></tr>


<tr>
                        <td><?php echo $entry_widget; ?></td>
                        <td> <select name="koraki_status" id="input-status" class="form-control">
                <?php if ($koraki_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
<td><?php echo $entry_widget_help; ?></td>
                    </tr>


		</table>
	    </form>
	</div>
  </div>
</div>
<?php echo $footer; ?>
