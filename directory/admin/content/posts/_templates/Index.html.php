<?php

echo $this->import->component('IndexHeaderBar', '~admin/content/posts/');

echo $this->import->component('PostList', '~admin/content/posts/', [
    'slug' => false,
    'title' => true
]);