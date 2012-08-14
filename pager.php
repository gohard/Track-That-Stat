<?php
class pager
{
function findStart($limit) {
    if ((!isset($_GET['pg'])) || ($_GET['pg'] == "1")) {
        $start = 0;
        $_GET['pg'] = 1;
    } else {
        $start = ($_GET['pg']-1) * $limit;
    }
    return $start;
}
 
  /*
   * int findPages (int count, int limit)
   * Returns the number of pages needed based on a count and a limit
   */
function findPages($count, $limit) {
     $pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1;
 
     return $pages;
}
 
/*
* string pageList (int curpage, int pages)
* Returns a list of pages in the format of "« < [pages] > »"
**/
function pageList($curpage, $pages)
{
    $page_list  = "";
 
    /* Print the first and previous page links if necessary */
    if (($curpage != 1) && ($curpage)) {
		$position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
		$page_list .= "  <a class='next-pre' href=\" ".$str."&pg=1\" title=\"First Page\"><<</a> ";
    }
 
    if (($curpage-1) > 0) {
		$position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
		$page_list .= "<a class='next-pre' href=\" ".$str."&pg=".($curpage-1)."\" title=\"Previous Page\"><</a> ";
    }
	

    /* Print the numeric page list; make the current page unlinked and bold */
    for ($i=1; $i<=$pages; $i++) {
        if ($i == $curpage) {
//            $page_list .= "<b>".$i."</b>";
            $page_list .= "<a class='page-select' href=\" ".$str."&pg=".$i."\" title=\"Page ".$i."\"><span class=\"page01\"></span><span class=\"page02\">".$i."</span><span class=\"page03\"></span></a>";

        } else {
			$position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}

			if($i-2<=0 || $pages-$i<2) {
				$page_list .= "<a class='page' href=\" ".$str."&pg=".$i."\" title=\"Page ".$i."\"><span class=\"page01\"></span><span class=\"page02\">".$i."</span><span class=\"page03\"></span></a>";
			}
			else {
				if($curpage-2==$i || $curpage-1==$i || $curpage+2==$i || $curpage+1==$i) {
					if($curpage-2==$i) {
						$page_list .= "<span class='page_span'>......</span>";
					}
					$page_list .= "<a class='page' href=\" ".$str."&pg=".$i."\" title=\"Page ".$i."\"><span class=\"page01\"></span><span class=\"page02\">".$i."</span><span class=\"page03\"></span></a>";
					if($curpage+2==$i) {
						$page_list .= "<span class='page_span'>......</span>";
					}
				}
				
			}
            
        }
        $page_list .= " ";
      }
 
     /* Print the Next and Last page links if necessary */
     if (($curpage+1) <= $pages) {
		 $position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
        $page_list .= "<a class='next-pre' href=\"".$str."&pg=".($curpage+1)."\" title=\"Next Page\">></a> ";
     }
 
     if (($curpage != $pages) && ($pages != 0)) {
		 $position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
        $page_list .= "<a class='next-pre' href=\"".$str."&pg=".$pages."\" title=\"Last Page\">>></a> ";
     }
     $page_list .= "\n";
 
     return $page_list;
}
 
/*
* string nextPrev (int curpage, int pages)
* Returns "Previous | Next" string for individual pagination (it's a word!)
*/
function nextPrev($curpage, $pages) {
 $next_prev  = "";
 
    if (($curpage-1) <= 0) {
        $next_prev .= "Previous";
    } else {
		$position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
        $next_prev .= "<a href=\"".$str."&pg=".($curpage-1)."\">Previous</a>";
    }
 
        $next_prev .= " | ";
 
    if (($curpage+1) > $pages) {
        $next_prev .= "Next";
    } else {
		$position = strpos($_SERVER['REQUEST_URI'],"&pg");
			if($position)
			{
				$str = substr($_SERVER['REQUEST_URI'],0,$position);
			}
			else
			{
				$str = $_SERVER['REQUEST_URI'];
			}
        $next_prev .= "<a href=\"".$str."&pg=".($curpage+1)."\">Next</a>";
    }
        return $next_prev;
   
}
}
?>