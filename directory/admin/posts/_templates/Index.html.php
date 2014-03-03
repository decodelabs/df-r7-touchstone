<?php

echo $this->import->component('IndexHeaderBar', '~admin/posts/');

echo $this->import->component('PostList', '~admin/posts/', [
    'slug' => false,
    'title' => true
]);