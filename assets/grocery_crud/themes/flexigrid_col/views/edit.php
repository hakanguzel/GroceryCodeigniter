<?php
$this->set_css($this->default_theme_path . '/flexigrid_col/css/custom.min.css');
$this->set_js_lib($this->default_javascript_path . '/jquery_plugins/jquery.form.min.js');
$this->set_js_config($this->default_theme_path . '/flexigrid_col/js/flexigrid-edit.js');

//$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.noty.js');
$this->set_js_lib($this->default_javascript_path . '/jquery_plugins/config/jquery.noty.config.js');
?>

<?php
//   dump_exit($fields);

$parentFlag = false;
$parentItems = [];
foreach ($fields as $key => $field) {
    $fieldConfig = $input_fields[$field->field_name];

    if ($fieldConfig->crud_type == 'readonly') {
        $parentItems[$field->field_name] = $fieldConfig;

        $childs = $fieldConfig->extras['childs'];
        foreach ($childs as $childName) {
            if (isset($input_fields[$childName])) {
                $parentFlag = true;
                $parentItems[$field->field_name]->childs[$childName] = $input_fields[$childName];
                unset($input_fields[$childName]);
            }
        }
    }
}

$inputItems = [];
foreach ($input_fields as $fieldName => $fieldItem) {
    if ($fieldItem->crud_type == 'readonly') {
        $inputItems[$fieldItem->name] = $parentItems[$fieldItem->name];
    } else {
        $inputItems[$fieldItem->name] = $fieldItem;
    }
}
$formType = $parentFlag == true ? 'vertical' : 'horizontal';
$quickAddFields = ['relation', 'select', 'multiselect'];
//   dump($quickAddFields);
?>


<div class="flexigrid crud-form box" style='width: 100%;' data-unique-hash="<?= $unique_hash; ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pencil fa-fw"></i> <?= $subject; ?> <?= $this->l('form_edit') . ' '; ?></h3>
    </div>

    <?php
    $formAttr = 'method="post" class="form-' . $formType . '" id="crudForm" autocomplete="off" enctype="multipart/form-data"';
    echo form_open($update_url, $formAttr);
    ?>
    <div id='main-table-box' class="box-body">
        <!---- rows start ----->
        <div class="row">
            <div class="col-md-12">
                <?php
                $focus = 'none';
                $i = 1;
                foreach ($inputItems as $filedName => $fieldItem) :
                    $childFlag = isset($fieldItem->childs);
                    if ($fieldItem->crud_type == 'readonly') :
                        echo '</div><div class="' . $fieldItem->extras['class'] . '">';
                    endif;
                ?>

                    <div class="row rowt-<?= $fieldItem->crud_type; ?>" id="<?= $filedName; ?>_field_box">
                        <div class='form-display-as-box col-sm-2 control-label' id="<?= $filedName; ?>_display_as_box">
                            <label>
                                <?= $fieldItem->display_as; ?><?= ($fieldItem->required) ? "<span class='required'>*</span> " : ''; ?>
                            </label>
                            <?
                            /**
                             <?php if ($fieldItem->crud_type === 'relation_n_n' &&  $fieldItem->extras->selection_table) : ?>
                                <a data-fancybox="quickAdd" data-type="iframe" data-options='{"modal":true,"iframe":{"css":{"width":"600px"}}}' href="<?= site_url('quick/item/' . uri_segment(2) . '/' .  $fieldItem->extras->selection_table . '/add'); ?>" class="quickAddBtn"><i class="fa fa-plus-circle"></i></a>
                            <?php endif; ?>
                             */
                            ?>
                        </div>
                        <div class='form-input-box col-sm-8' id="<?= $filedName; ?>_input_box">
                            <?= $fieldItem->input; ?>
                        </div>
                    </div>
                    <br>
                    <?php if ($childFlag) : ?>
                        <?php foreach ($fieldItem->childs as $childFiledName => $cildField) : ?>
                            <div class="row" id="<?= $childFiledName; ?>_field_box">
                                <div class='form-display-as-box col-sm-12 control-label' id="<?= $childFiledName; ?>_display_as_box">
                                    <label>
                                        <?= $cildField->display_as; ?><?= ($cildField->required) ? "<span class='required'>*</span> " : ''; ?>
                                    </label>
                                    <!-- | -->
                                    <?php if (
                                        in_array($cildField->crud_type, $quickAddFields) &&
                                        in_array($childFiledName, $fieldItem->extras['allow_add'])
                                    ) : ?>
                                        <a data-fancybox="quickAdd" data-type="iframe" data-options='{"modal":true,"iframe":{"css":{"width":"600px"}}}' href="<?= site_url('quick/item/' . uri_segment(2) . '/' . $childFiledName . '/add'); ?>" class="quickAddBtn"><i class="fa fa-plus-circle"></i></a>
                                    <?php endif; ?>
                                    <!-- | -->
                                </div>
                                <div class='form-input-box col-sm-12' id="<?= $childFiledName; ?>_input_box">
                                    <?= $cildField->input; ?>
                                </div>
                            </div>
                            <br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php
                    if ($i == 1) :
                        $focus = 'field-' . $fieldName;
                    endif;
                    ++$i;
                endforeach;
                ?>
            </div>
        </div>
        <!---- rows end ----->
        <!-- Start of hidden inputs -->
        <?php
        if (!empty($hidden_fields)) :
            foreach ($hidden_fields as $hidden_field) :
                echo $hidden_field->input;
            endforeach;
        endif;
        ?>
        <!-- End of hidden inputs -->
        <?php if ($is_ajax) : ?><input type="hidden" name="is_ajax" value="true" /><?php endif; ?>
        <div id='report-error' class='report-div error alert alert-danger' role="alert"></div>
        <div id='report-success' class='report-div success alert alert-success'></div>
    </div>

    <div class="box-footer">
        <div class="row">
            <div class="col-sm-6">
                <p>
                    <button type="submit" id="form-button-save" class="btn btn-primary btn-flat"><?= $this->l('form_save'); ?></button> &nbsp;
                    <?php if (!$this->unset_back_to_list) : ?>
                        <!-- <button type="button" id="save-and-go-back-button" class="btn btn-info btn-flat"><?= $this->l('form_save_and_go_back'); ?></button> &nbsp; -->
                        <button type="button" id="cancel-button" class="btn btn-default btn-flat"><?= $this->l('form_cancel'); ?></button>
                    <?php endif; ?>
                    <span class='small-loading' id='FormLoading'><img src="<?= base_url('assets/img/svg/loading-spin-primary.svg'); ?>" alt="loading..."> <?= $this->l('form_insert_loading'); ?></span>
                </p>
            </div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
    var validation_url = '<?= $validation_url; ?>';
    var list_url = '<?= $list_url; ?>';
    var focus = '<?= $focus; ?>';
    var message_alert_edit_form = "<?= $this->l('alert_edit_form'); ?>";
    var message_update_error = "<?= $this->l('update_error'); ?>";
</script>