<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\migrate\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class TaskUpdatePostDate extends arch\node\Task {

    public function execute() {
        $this->io->write('Updating dates...');
        $count = 0;
        $posts = $this->data->touchstone->post->fetch();

        foreach($posts as $post) {
            $post->postDate = clone $post['creationDate'];
            $post->save();
            $count++;
        }

        $this->io->writeLine(' '.$count.' posts');
    }
}