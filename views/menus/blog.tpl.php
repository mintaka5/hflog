<div class="nav-items">
    <ul>
        <? foreach($this->blogs as $blog): ?>
        <li>
            <a href="<?= $this->manager->friendlyAction("blog", "view", $blog->post_name); ?>" title="<?= $blog->post_title; ?>"><?= $blog->post_title; ?></a>
        </li>
        <? endforeach; ?>
    </ul>
</div>
<div class="nav-desc">
    <?= $this->blogs[0]->post_excerpt; ?>
</div>