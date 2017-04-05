<?php
$this->assign('title', 'Search Blogs');
$this->Html->script('search', ['block' => true]);
?>
<div class="row">
    <div class="large-offset-2 large-8 columns content">
        <form name="frmSortBlogs" method="post" action="/search">
            <div style="display:none;">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="sb" value="<?= $post_data['sb'] ?? '' ?>">
                <input type="hidden" name="sd" value="<?= $post_data['sd'] ?? '' ?>">
                <input type="hidden" name="published_date" value="<?= $post_data['published_date'] ?? '' ?>">
                <input type="hidden" name="search" value="<?= $post_data['search'] ?? '' ?>">
                <input type="hidden" name="user" value="<?= $post_data['user'] ?? '' ?>">
            </div>
        </form>
        <form name="frmViewBlogs" method="post" action="">
            <div style="display:none;">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="sb" value="<?= $post_data['sb'] ?? '' ?>">
                <input type="hidden" name="sd" value="<?= $post_data['sd'] ?? '' ?>">
                <input type="hidden" name="published_date" value="<?= $post_data['published_date'] ?? '' ?>">
                <input type="hidden" name="search" value="<?= $post_data['search'] ?? '' ?>">
                <input type="hidden" name="user" value="<?= $post_data['user'] ?? '' ?>">
            </div>
        </form>
        <form name="frmSearchBlogs" method="post" action="/search">
            <div style="display:none;">
                <input type="hidden" name="_method" value="POST">
            </div>
            <div class="row valign-end">
                <div class="medium-3 columns">
                    <label>Published date
                    <input type="text" name="published_date" id="search-published-date" placeholder="mm/dd/yyyy" maxlength="10" value="<?= $post_data['published_date'] ?? '' ?>">
                    </label>
                </div>
                <div class="medium-3 columns">
                    <label>Title or content
                    <input type="text" name="search" placeholder="title or content" value="<?= $post_data['search'] ?? '' ?>">
                    </label>
                </div>
                <div class="medium-3 columns">
                    <label>Comments by
                    <select name="user">
                        <option value="">--Select one--</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"<?= ($post_data['user'] == $user['id']) ? ' selected' : '' ?>><?= $user['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    </label>
                </div>
                <div class="medium-3 columns">
                <button type="submit" class="success button">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
<hr>
<div class="row">
    <div class="large-offset-2 large-8 columns content">
        <table class="hover">
            <thead>
                <th><a href="#" class="sort-link" data-col="title">Title</a></th>
                <th>Author</th>
                <th><a href="#" class="sort-link" data-col="date">Published Date</a></th>
            </thead>
            <tbody>
        <?php if (empty($results)): ?>
                <tr>
                    <td colspan="3">No results found.</td>
                </tr>
        <?php else: ?>
            <?php foreach ($results as $post): ?>
                <tr>
                    <td><?= $this->Html->link($post['title'], '#', ['class' => 'view-blog', 'data-blog-id' => $post['id']]) ?></td>
                    <td><?= $this->Html->link($post['author']['name'], $post['author']['profile'], ['target' => '_blank']) ?></td>
                    <td><?= $post['published_date'] ?></td>
                <tr>
            <?php endforeach; ?>
        <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>