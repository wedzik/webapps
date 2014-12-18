<?php

class Text2htmlMailer {

    protected $text;
    protected $output;
    protected $schars;
    protected $opened;
    protected $op;
    protected $tc;
    protected $paragraphs;
    protected $paragraph;
    protected $big_tags;

    function __construct() {
        $this->big_tags = array("p", "ul");
        foreach ($this->big_tags as $bt) {
            $this->opened[$bt] = false;
        }
        $this->schars = array(
            "&" => "&amp;",
            "->" => "&rarr;",
            "-&gt;" => "&rarr;",
            "<-" => "&larr;",
            "&lt;-" => "&larr;",
            "<" => "&lt;",
            ">" => "&gt;",
            //"--"    => "&ndash;",
            "..." => "&hellip;",
            "(c)" => "&copy;",
            "(tm)" => "&trade;",
            "(R)" => "&reg;"
        );
    }

    function parse($text) {
        $text = strip_tags($text);
        $this->output = Array();
        $this->text = str_replace("\r", "", trim($text));
        $this->text = preg_replace("/( [skvzuoai]) /i", "\\1 ", $this->text); // czech typography
        $this->text = preg_replace("/&([a-z]{2,6};)/i", "&\\1", $this->text);
        $this->paragraphs = explode("(\n){2,}", $this->text);
        foreach ($this->paragraphs as $paragraph) {
            $this->output[] = $this->parse_paragraph($paragraph);
        }
        return join("\n", $this->output);
    }

    function parse_paragraph($text) {


        $rows = explode("\n", $text);

        $this->paragraph = "";

        foreach ($rows as $row) {

            $row = trim($row);

            if (preg_match("/^#/", $row)) {
                if (preg_match("/<br />$/", $this->paragraph)) {
                    $this->paragraph = substr($this->paragraph, 0, -6);
                }
                if ($this->opened['p'] === true) {
                    $this->paragraph .= "</p>\n";
                    $this->opened['p'] = false;
                }
                if ($this->opened['ul'] === false) {
                    $this->paragraph .= "<ul>\n";
                    $this->opened['ul'] = true;
                }
                $this->paragraph .= "  <li>" . $this->parse_row(trim(substr($row, 1))) . "</li>\n";
            } else {
                if ($this->opened['ul'] === true) {
                    $this->paragraph .= "</ul>\n";
                    $this->opened['ul'] = false;
                }
                if ($this->opened['p'] === false) {
                    $this->paragraph .= "<p>";
                    $this->opened['p'] = true;
                }
                $this->paragraph .= $this->parse_row($row) . "<br/>\n";
            }
        }

        if (preg_match("/$/", $this->paragraph)) {
            $this->paragraph = substr($this->paragraph, 0, -6);
        }

        if ($this->opened['ul'] === true) {
            $this->paragraph .= "</ul>\n";
            $this->opened['ul'] = false;
        } elseif ($this->opened['p'] === true) {
            $this->paragraph .= "</p>\n";
            $this->opened['p'] = false;
        }
        return $this->paragraph;
    }

    function parse_row($row) {

        $this->op = "";

        $tags = array(
            "**" => "strong",
            //"//" => "em",
            "^^" => "sup",
            "__" => "sub",
            "++" => "ins",
            "--" => "del",
            "??" => "cite"
        );

        foreach ($tags as $tag) {
            $this->op[$tag] = false;
            $this->tc[$tag] = 0;
        }

        $position = array();
        $at = NULL;
        if (strlen($row) > 0) {
            for ($i = 0; $i < strlen($row); $i++) {
                if ($i > 100)
                    break;
                foreach ($tags as $tag => $tagg) {
                    $tp = strpos($row, $tag);
                    if ($tp == $i && $tp !== false) {
                        $at = $tag;
                    }
                }

                if (isset($at)) {
                    $entity = $at;
                }
                if (isset($tags[$at])) {
                    $at = $tags[$at];
                }

                if (empty($at))
                    continue;

                if (($this->tc[$at] / 2) == 0) {
                    $replace = "<$at>";
                    $this->op[$at] = true;
                } else {
                    $replace = "";
                    $this->op[$at] = false;
                }

                $this->tc[$at]++;

                $opened = array();
                foreach ($this->op as $str => $status) {
                    if ($status === true && $str <> $at) {
                        $opened[] = $str;
                    }
                }

                array_reverse($opened);

                $bef = 0;
                $aft = 0;

                $before = substr($row, 0, $i);

                $after = substr($row, $i + strlen($entity));

                foreach ($opened as $ope) {
                    $tags = array_flip($tags);
                    $ent = $tags[$ope];
                    $tags = array_flip($tags);
                    if (strpos($after, $ent) === 0) {
                        $replace = "$replace";
                        $this->op[$ope] = false;
                        $after = substr($after, strlen($entity));
                    } elseif (strrpos($after, $ope) == strlen($after) - strlen($ope) - 1) {
                        $replace = $replace . "<$ope>";
                        $before = substr($before, 0, -(strlen($ope) + 2));
                    } else {
                        $replace = "$replace<$ope>";
                    }
                }


                $row = $before . $replace . $after;
            }
        }

        foreach ($this->op as $op => $vas) {
            if ($vas === true) {
                $row .= "";
            }
            $row = str_replace("<$op>", "", $row);
        }
        return $row;
    }

}

?>