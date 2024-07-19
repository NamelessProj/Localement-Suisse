<?php

// PAGINATION SYSTEM
if(!isset($maxPaginationLinks)) $maxPaginationLinks = 5;
if(!isset($page)) $page = 1;
if(!isset($pages_count)) $pages_count = 1;
if(!isset($linkPath)) $linkPath = "search.php"; // NAME OF THE FILE

$is_first_page = $page === 1; // WE CHECK IF WE'RE AT THE FIRST PAGE OR NOT
$is_last_page = $page === $pages_count; // WE CHECK IF WE'RE AT THE LAST PAGE OR NOT

// PREV CAN'T BE LESS THAN 1
$prev_page = max(1, $page - 1);
// NEXT CAN'T BE LARGER THAN $page_count
$next_page = min($pages_count, $page + 1);

// IF WE HAVE MORE THAN 1 PAGE, WE DISPLAY THE PAGINATION
if($page < $pages_count || $pages_count > 1):
    $maxPageLinks = min($pages_count, $maxPaginationLinks); // SETTING THE NUMBER OF LINKS TO 5 OR $pages_count IF IT'S SMALLER
    $currentPageLink = $page < $maxPageLinks / 2 ? 1 : 0; // THE CURRENT PAGE FOR THE PAGINATION IS SET TO 0 BY DEFAULT BUT IS SET TO 1 IF THE CURRENT PAGE NUMBER IS LESS THAN THE HALF OF THE MAX NUMBER OF LINKS

    // SETTING WHERE THE LOOP WILL START
    // IF THE CURRENT PAGE NUMBER IS LESS THAN THE HALF OF THE MAX LINK, THE LOOP NEED TO START AT THE INDEX 1
    // ELSE, THE LOOP SHOULD START AT THE INDEX OF THE CURRENT PAGE NUMBER MINUS THE HALF OF THE MAX LINK FLOORED
    /*
     * $pages_count = 50;
     * $maxPageLinks = 5;
     * $page = 7
     * $firstIndex = $page - floor($maxPageLinks / 2);
     *
     * 5 = 7 - 2;
     *
     * (so the loop will start at the index 5, that way, the current page is in the middle)
     * [5][6][7][8][9]
     */
    $firstIndex = $page <= $maxPageLinks / 2 ? 1 : $page - floor($maxPageLinks / 2);

    // IN CASE THAT $firstIndex IS EQUAL OR GREATER THAN THE NUMBER OF PAGES MINUS THE MAX LINKS NUMBER, WE SUBTRACT THE MAX LINKS NUMBER PLUS 1 TO THE NUMBER OF PAGES
    /*
     * $pages_count = 50;
     * $maxPageLinks = 5;
     * $firstIndex = 47
     *
     * 47 is greater than 50 - 5
     * 47 > 45
     *
     * $firstIndex = $pages_count - $maxPageLinks + 1;
     * 46 = 50 - 5 + 1
     *
     * (so the loop will start at the index 46, that way, we don't go further than the maximum page we have)
     * [46][47][48][49][50]
     */
    if($firstIndex > $pages_count - $maxPageLinks) $firstIndex = $pages_count - $maxPageLinks + 1;
    ?>
    <div class="pagination-container">
        <ul>
            <?php
            if($page > 1):
                // IF WE ARE CURRENTLY TO PAGE GREATER THAN 1, WE ADD AN ARROW TO GO DOWN 1 PAGE

                // ADDING THE FULL REQUEST IN THE URL
                $prevPage = "page=".$page - 1;
                ?>
                <li>
                    <a href="./<?= $linkPath.$prevPage ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // PUTTING 3 DOTS IF WE'VE PAST 5 PAGES IF THE FIRST PAGES AREN'T VISIBLE
            if($maxPageLinks === $maxPaginationLinks && $page > ($maxPageLinks / 2) + 1): ?>
                <li class="no-style">
                    <p>...</p>
                </li>
            <?php endif; ?>

            <?php
            // LOOP FOR THE PAGINATION
            for($i = 0; $i < $maxPageLinks; $i++):
                // ADDING THE FULL REQUEST IN THE URL
                $links = "page=".$firstIndex;
                ?>
                <li>
                    <a href="./<?= $linkPath.$links ?>" <?php if($page == $firstIndex){echo "class='current-page'";} ?>><?= $firstIndex ?></a>
                </li>
                <?php
                $firstIndex++; // WE INCREMENT THE INDEX
            endfor;
            ?>

            <?php
            // PUTTING 3 DOTS AND A DIRECT LINK TO THE LAST PAGE IF WE HAVEN'T PAST 5 PAGES AND THE LAST PAGE LINK IS NOT VISIBLE
            if($pages_count > $maxPageLinks && ($pages_count - $maxPageLinks + floor($maxPageLinks / 2)) >= $page):
                $links = "page=".$pages_count;
                ?>
                <li class="no-style">
                    <p>...</p>
                </li>
                <li>
                    <a href="./<?= $linkPath.$links ?>"><?= $pages_count ?></a>
                </li>
            <?php endif; ?>

            <?php
            if($page < $pages_count):
                // IF WE ARE CURRENTLY TO PAGE LOWER THAN $pages_count, WE ADD AN ARROW TO GO UP 1 PAGE

                // ADDING THE FULL REQUEST IN THE URL
                $nextPage = "page=".$page + 1;
                ?>
                <li>
                    <a href="./<?= $linkPath.$nextPage ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                            <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>