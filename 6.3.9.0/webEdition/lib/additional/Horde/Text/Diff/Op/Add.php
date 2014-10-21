<?php
/**
 * The original PHP version of this code was written by Geoffrey T. Dairiki
 * <dairiki@dairiki.org>, and is used/adapted with his permission.
 *
 * Copyright 2004 Geoffrey T. Dairiki <dairiki@dairiki.org>
 * Copyright 2004-2012 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @package none
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Horde_Text_Diff_Op_Add extends Horde_Text_Diff_Op_Base
{
    public function __construct($lines)
    {
        $this->final = $lines;
        $this->orig = false;
    }

    public function reverse()
    {
        return new Horde_Text_Diff_Op_Delete($this->final);
    }
}
