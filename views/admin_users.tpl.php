<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <h2>Users</h2>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
        <?php if ($this->manager->isMode()): ?>
            <div>
                <?php if ($this->manager->isTask()): ?>
                    <script type="text/javascript"
                            src="<?php echo APP_ASSETS_URL; ?>assets/js/hflogs/admin/users/default.js"></script>
                    <div>
                        <?php if (!empty($this->users)): ?>
                            <table class="table table-striped">
                                <thead></thead>
                                <tbody>
                                <?php foreach ($this->users as $num => $user): ?>
                                    <tr style="<?php echo ($user->is_deleted == 1) ? 'background-color:red;' : ''; ?>">
                                        <td>
                                            <input type="checkbox" class="chkUser"
                                                   dat-userid="<?php echo $user->id; ?>"/>
                                        </td>
                                        <td>
                                            <div><strong><?php echo $user->fullname(); ?></strong></div>
                                            <div><?php echo $user->username; ?></div>
                                            <div><?php echo $user->email; ?></div>
                                            <div><?php echo $user->id; ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="form-group">
                                                    <label class="control-label"
                                                           for="userType_<?php echo $num; ?>">Type:</label>
                                                    <?php if (!$user->isAdmin()): // don't make admin users editable ?>
                                                        <select id="userType_<?php echo $num; ?>"
                                                                class="userTypeSel form-control"
                                                                data-userid="<?php echo $user->id; ?>">
                                                            <option value="0">Guest</option>
                                                            <?php foreach ($this->usertypes as $usertype): ?>
                                                                <option <?php echo ($user->type()->id == $usertype->id) ? 'selected' : ''; ?>
                                                                    value="<?php echo $usertype->id ?>"><?php echo $usertype->title; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        Administrator
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <label>Account Created:</label>
                                                <?php echo date('F jS, Y', strtotime($user->created)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!$user->isAdmin()): // don't delete or disable admin accounts ?>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button"
                                                            data-href="<?php echo $this->manager->friendlyAction('admin_hflogs', 'users', 'delete', array('id', $user->id)); ?>"
                                                            class="disableUser btn btn-default">Disable
                                                    </button>
                                                    <button type="button" class="removeUser btn btn-default"
                                                            data-id="<?php echo $user->id; ?>">Remove
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            &nbsp;
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; // end default mode ?>
    </div>
</div>
