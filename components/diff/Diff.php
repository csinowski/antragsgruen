<?php

namespace app\components\diff;

use app\models\exceptions\Internal;

class Diff
{
    const ORIG_LINEBREAK = '###ORIGLINEBREAK###';

    private $debug = false;

    /** @var Engine */
    private $engine;

    public function __construct()
    {
        $this->engine = new Engine();
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $str
     */
    public function setIgnoreStr($str)
    {
        $this->engine->setIgnoreStr($str);
    }

    /**
     * @param string $str
     * @return string
     */
    private function wrapWithInsert($str)
    {
        if ($str == '') {
            return $str;
        } elseif (preg_match('/^<[^>]*>$/siu', $str)) {
            return $str;
        } elseif ($str == static::ORIG_LINEBREAK) {
            return $str;
        }
        if (mb_stripos($str, '<ul>') === 0) {
            return '<ul class="inserted">' . mb_substr($str, 4);
        } elseif (mb_stripos($str, '<ol>') === 9) {
            return '<ol class="inserted">' . mb_substr($str, 4);
        } elseif (mb_stripos($str, '<ul>')) {
            return '<li class="inserted">' . mb_substr($str, 12);
        } elseif (mb_stripos($str, '<blockquote>')) {
            return '<blockquote class="inserted">' . $str;
        } else {
            return '<ins>' . $str . '</ins>';
        }
    }

    /**
     * @param string $str
     * @return string
     */
    private function wrapWithDelete($str)
    {
        if ($str == '') {
            return '';
        } elseif (preg_match('/^<[^>]*>$/siu', $str)) {
            return $str;
        } elseif ($str == static::ORIG_LINEBREAK) {
            return $str;
        }
        if (mb_stripos($str, '<ul>') === 0) {
            return '<ul class="deleted">' . mb_substr($str, 4);
        } elseif (mb_stripos($str, '<ol>') === 9) {
            return '<ol class="deleted">' . mb_substr($str, 4);
        } elseif (mb_stripos($str, '<ul>')) {
            return '<li class="deleted">' . mb_substr($str, 12);
        } elseif (mb_stripos($str, '<blockquote>')) {
            return '<blockquote class="deleted">' . $str;
        } else {
            return '<del>' . $str . '</del>';
        }
    }

    /**
     * @param string $line
     * @return string[]
     */
    private function tokenizeLine($line)
    {
        $line = str_replace(" ", " \n", $line);
        $line = str_replace("<", "\n<", $line);
        $line = str_replace(">", ">\n", $line);
        return $line;
    }

    /**
     * @param string $line
     * @return string
     */
    private function untokenizeLine($line)
    {
        $line = str_replace("\n", '', $line);
        return $line;
    }

    /**
     * @param array $operations
     * @param string $groupBy
     * @return array
     */
    public function groupOperations($operations, $groupBy)
    {
        $return = [];

        $preOp        = null;
        $currentSpool = [];
        foreach ($operations as $operation) {
            if ($operation[0] == static::ORIG_LINEBREAK || preg_match('/^<[^>]*>$/siu', $operation[0])) {
                if (count($currentSpool) > 0) {
                    $return[] = [
                        implode($groupBy, $currentSpool),
                        $preOp
                    ];
                }
                $return[]     = [
                    $operation[0],
                    $operation[1],
                ];
                $preOp        = null;
                $currentSpool = [];
            } elseif ($operation[1] !== $preOp) {
                if (count($currentSpool) > 0) {
                    $return[] = [
                        implode($groupBy, $currentSpool),
                        $preOp
                    ];
                }
                $preOp = $operation[1];
                if ($operation[0] != '') {
                    $currentSpool = [$operation[0]];
                }
            } else {
                $currentSpool[] = $operation[0];
            }
        }
        if (count($currentSpool) > 0) {
            $return[] = [
                implode($groupBy, $currentSpool),
                $preOp
            ];
        }

        return $return;
    }

    /**
     * @param string $word1
     * @param string $word2
     * @return string
     */
    private function getCommonPrefix($word1, $word2)
    {
        $len1 = mb_strlen($word1);
        $len2 = mb_strlen($word2);
        $min  = min($len1, $len2);
        $str  = '';
        for ($i = 0; $i <= $min; $i++) {
            $char1 = mb_substr($word1, $i, 1);
            $char2 = mb_substr($word2, $i, 1);
            if ($char1 != $char2) {
                return $str;
            } else {
                $str .= $char1;
            }
        }
        return $str;
    }

    /**
     * @param string $word1
     * @param string $word2
     * @return string
     */
    private function getCommonSuffix($word1, $word2)
    {
        $len1 = mb_strlen($word1);
        $len2 = mb_strlen($word2);
        $min  = min($len1, $len2);
        $str  = '';
        for ($i = 0; $i <= $min; $i++) {
            $char1 = mb_substr($word1, $len1 - $i, 1);
            $char2 = mb_substr($word2, $len2 - $i, 1);
            if ($char1 != $char2) {
                return $str;
            } else {
                $str = $char1 . $str;
            }
        }
        return $str;
    }

    /**
     * @param string $wordDel
     * @param string $wordInsert
     * @return string
     */
    private function computeWordDiff($wordDel, $wordInsert)
    {
        $pre     = $this->getCommonPrefix($wordDel, $wordInsert);
        $restDel = mb_substr($wordDel, mb_strlen($pre));
        $restIns = mb_substr($wordInsert, mb_strlen($pre));

        $post    = $this->getCommonSuffix($restDel, $restIns);
        $restDel = mb_substr($restDel, 0, mb_strlen($restDel) - mb_strlen($post));
        $restIns = mb_substr($restIns, 0, mb_strlen($restIns) - mb_strlen($post));

        return $pre . $this->wrapWithDelete($restDel) . $this->wrapWithInsert($restIns) . $post;
    }

    /**
     * @param string $lineOld
     * @param string $lineNew
     * @return string
     * @throws Internal
     */
    public function computeLineDiff($lineOld, $lineNew)
    {
        $computedStrs = [];
        $lineOld      = $this->tokenizeLine($lineOld);
        $lineNew      = $this->tokenizeLine($lineNew);

        $return = $this->engine->compare($lineOld, $lineNew);
        $return = $this->groupOperations($return, '');

        for ($i = 0; $i < count($return); $i++) {
            if ($return[$i][1] == Engine::UNMODIFIED) {
                $computedStrs[] = $return[$i][0];
            } elseif ($return[$i][1] == Engine::DELETED) {
                if (isset($return[$i + 1]) && $return[$i + 1][1] == Engine::INSERTED) {
                    $computedStrs[] = $this->computeWordDiff($return[$i][0], $return[$i + 1][0]);
                    $i++;
                } else {
                    $delParts = explode("\n", str_replace(" ", " \n", $return[$i][0]));
                    foreach ($delParts as $delPart) {
                        $computedStrs[] = $this->wrapWithDelete($delPart);
                    }
                }
            } elseif ($return[$i][1] == Engine::INSERTED) {
                $insParts = explode("\n", str_replace(" ", " \n", $return[$i][0]));
                foreach ($insParts as $insPart) {
                    $computedStrs[] = $this->wrapWithInsert($insPart);
                }
            } else {
                throw new Internal('Unknown type: ' . $return[$i][1]);
            }
        }
        $computedStr = implode("\n", $computedStrs);
        if ($this->debug) {
            var_dump($computedStr);
        }

        echo "\n\n---\n";
        var_dump($computedStr);
        echo "\n---\n";


        $combined = $this->untokenizeLine($computedStr);
        $combined = str_replace('</del> <del>', ' ', $combined);
        $combined = str_replace('</del><del>', '', $combined);
        $combined = str_replace('</ins> <ins>', ' ', $combined);
        $combined = str_replace('</ins><ins>', '', $combined);

        if ($this->debug) {
            var_dump($combined);
            die();
        }
        return $combined;
    }

    /**
     * @param string $strOld
     * @param string $strNew
     * @return string
     * @throws Internal
     */
    public function computeDiff($strOld, $strNew)
    {
        $computedStr = '';

        $return = $this->engine->compare($strOld, $strNew);
        if ($this->debug) {
            echo "==========\n";
            var_dump($return);
            echo "\n\n\n";
        }
        $return = $this->groupOperations($return, static::ORIG_LINEBREAK);
        for ($i = 0; $i < count($return); $i++) {
            if ($return[$i][1] == Engine::UNMODIFIED) {
                $computedStr .= $return[$i][0] . "\n";
            } elseif ($return[$i][1] == Engine::DELETED) {
                if (isset($return[$i + 1]) && $return[$i + 1][1] == Engine::INSERTED) {
                    $computedStr .= $this->computeLineDiff($return[$i][0], $return[$i + 1][0]);
                    $i++;
                } else {
                    $computedStr .= $this->wrapWithDelete($return[$i][0]) . "\n";
                }
            } elseif ($return[$i][1] == Engine::INSERTED) {
                $computedStr .= $this->wrapWithInsert($return[$i][0]) . "\n";
            } else {
                throw new Internal('Unknown type: ' . $return[$i][1]);
            }
        }
        $computedStr = str_replace(static::ORIG_LINEBREAK, "\n", $computedStr);

        if ($this->debug) {
            die();
        }

        return trim($computedStr);
    }
}