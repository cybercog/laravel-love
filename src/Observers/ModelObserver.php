<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Observers;

use Cog\Likeable\Contracts\HasLikes as HasLikesContract;

/**
 * Class ModelObserver.
 *
 * @package Cog\Likeable\Observers
 */
class ModelObserver
{
    /**
     * Handle the deleted event for the model.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function deleted(HasLikesContract $model)
    {
        if (!$this->removeLikesOnDelete($model)) {
            return;
        }

        $model->removeLikes();
    }

    /**
     * Should remove likes on model delete (defaults to true).
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return bool
     */
    protected function removeLikesOnDelete(HasLikesContract $model)
    {
        return isset($model->removeLikesOnDelete) ? $model->removeLikesOnDelete : true;
    }
}
