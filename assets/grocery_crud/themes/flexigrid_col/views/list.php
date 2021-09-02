<?php if (!empty($list)) : ?>
    <div class="bDiv">
        <table class="table table-bordered table-hover" id="flex1">
            <thead>
                <tr class='hDiv'>
                    <?php if (!$unset_delete) : ?>
                        <th><input type="checkbox" value="all" class="check check-table" /></th>
                    <?php endif; ?>
                    <?php if (!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)) : ?>
                        <th abbr="tools" axis="col1"><?= $this->l('list_actions'); ?></th>
                    <?php endif; ?>
                    <?php foreach ($columns as $column) : ?>
                        <th class="field-sorting <?php if (isset($order_by[0]) && $column->field_name == $order_by[0]) : ?><?= ' ' . $order_by[1]; ?><?php endif; ?>" rel='<?= $column->field_name; ?>'>
                            <?= $column->display_as; ?> <span class="sort pull-right"><i class="fa fa-sort"></i></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $num_row => $row) : ?>
                    <tr>
                        <?php if (!$unset_delete) : ?>
                            <td>
                                <input type="checkbox" value="<?= $row->primary_key_value; ?>" class="check check-table" />
                            </td>
                        <?php endif; ?>

                        <?php if (!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)) : ?>
                            <td class="td-action">
                                <ul class='tools list-unstyled table-menu'>
                                    <?php if (!$unset_read) : ?>
                                        <li>
                                            <a href='<?= $row->read_url; ?>' title='<?= $subject; ?> <?= $this->l('list_view'); ?>' class="edit_button"><i class='fa fa-eye'></i></a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (!$unset_edit && ($row->id_user === $_SESSION['user_id']) || in_array('1', $_SESSION['user_groups'])) : ?>
                                        <li>
                                            <a href='<?= $row->edit_url; ?>' title='<?= $subject; ?> <?= $this->l('list_edit'); ?>' class="edit_button"><i class='fa fa-pencil'></i></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php
                                    if (!empty($row->action_urls)) :
                                        foreach ($row->action_urls as $action_unique_id => $action_url) :
                                            $action = $actions[$action_unique_id];
                                    ?>
                                            <li>
                                                <a href="<?= $action_url; ?>" class="<?= $action->css_class; ?> crud-action" title="<?= $action->label; ?>"><i class="<?= $action->image_url; ?>"></i></a>
                                            </li>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </ul>
                            </td>
                        <?php endif; ?>
                        <?php foreach ($columns as $column) : ?>
                            <td><?= $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <br />
    <?= $this->l('list_no_items'); ?>
    <br />
    <br />
<?php endif; ?>