<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Liker\Models;

use Cog\Contracts\Love\Likeable\Models\Likeable;

/**
 * Interface Liker.
 *
 * @package Cog\Contract\Likeable\Liker\Models
 */
interface Liker
{
    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function like(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function unlike(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function dislike(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function undislike(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function toggleLike(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function toggleDislike(Likeable $likeable);

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasLiked(Likeable $likeable): bool;

    /**
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasDisliked(Likeable $likeable): bool;
}
