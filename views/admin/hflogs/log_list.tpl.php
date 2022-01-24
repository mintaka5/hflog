<?php if (!empty($this->logs)): ?>
    <form method="post" action="<?php echo $this->manager->action('admin_hflogs', 'process'); ?>">
        <table class="table table-striped table-bordered">
            <tbody>
            <?php foreach ($this->logs as $log): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="log[]" value="<?php echo $log->id; ?>"
                               class="checkboxLog checkbox">
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-lg-7">
                                <div>
                                    <strong><?php echo $log->freq(); ?>&nbsp;<?php echo $log->mode; ?></strong>
                                </div>
                                <div>
                                    <small><?php echo $this->date($log->time_on, 'D M d, Y H:i'); ?></small>
                                </div>
                                <div>
                                    <small><?php echo $log->description(); ?></small>
                                </div>
                                <div>
                                    <?php echo $log->user()->fullname(); ?> - <?php echo $log->user()->username; ?>
                                    (<?php echo $log->user()->typeTitle(); ?>)
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div>
                                    Submitted: <?php echo $this->date($log->submitted, 'D M d, Y'); ?>
                                </div>
                                <?php if ($log->hasLocation()): ?>
                                    <div>
                                        <strong><?php echo $log->location()->location()->station()->title; ?></strong>
                                    </div>
                                    <div><?php echo $log->location()->location()->site; ?></div>
                                    <div>
                                        <em><a target="_blank"
                                               href="//maps.google.com/?q=<?php echo $log->location()->location()->coordinates()->toCoordinate(); ?>&z=6"><?php echo $log->location()->location()->coordinates()->toDegrees(); ?></a></em>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <ul class="nav nav-pills">
                            <li>
                                <a href="<?php echo $this->manager->action('admin_hflogs', 'log', 'approve', array('id', $log->id)); ?>">Approve</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('admin_hflogs', 'log', 'unapprove', array('id', $log->id)); ?>">Disapprove</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('admin_hflogs', 'log', 'delete', array('id', $log->id)); ?>">Delete</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('logs', 'log', 'edit', array('id', $log->id)); ?>">Edit</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->action('logs', 'log', 'view', array('id', $log->id)); ?>">View</a>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div>With selected:</div>
        <ul class="nav nav-pills">
            <li>
                <button class="btn btn-default" type="submit" name="act" value="<?php echo \Ode\DBO\Hflog\Status::STATUS_APPROVED; ?>">Approve</button>
            </li>
            <li>
                <button type="submit" class="btn btn-warning" name="act" value="<?php echo \Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED; ?>">Disapprove</button>
            </li>
            <li>
                <button type="submit" name="act" value="delete" class="btn btn-danger">Delete</button>
            </li>
        </ul>
    </form>
<?php else: ?>
    <div>Nothing to do here.</div>
<?php endif; ?>