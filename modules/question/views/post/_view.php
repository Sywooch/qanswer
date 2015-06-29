<div id="question-summary-<?php echo $data->id; ?>" class="question-summary">
    <div class="statscontainer">
        <div class="stats">
            <div class="vote">
                <div class="votes">
                    <span class="vote-count-post"><strong><span style="font-size: 80%;">2446</span></strong></span>
                    <div class="viewcount">votes</div>
                </div>
            </div>
            <div class="status answered">
                <strong><?php echo $data->answercount; ?></strong>answers
            </div>
        </div>
        <div title="查看<?php echo $data->viewcount; ?>次" class="views supernova">查看<?php echo $data->viewcount; ?>次</div>
    </div>
    <div class="summary">        
        <h3><a class="question-hyperlink" href="http://stackoverflow.com/questions/194812/list-of-freely-available-programming-books"><?php echo $data->title; ?></a></h3>
        <div class="excerpt">
			<?php echo $data->content; ?>
        </div>
                     
        <div class="tags t-books t-free t-ebook t-creative-commons t-textbooks">
            <a rel="tag" title="show questions tagged 'books'" class="post-tag" href="http://stackoverflow.com/questions/tagged/books">books</a> <a rel="tag" title="show questions tagged 'free'" class="post-tag" href="http://stackoverflow.com/questions/tagged/free">free</a> <a rel="tag" title="show questions tagged 'ebook'" class="post-tag" href="http://stackoverflow.com/questions/tagged/ebook">ebook</a> <a rel="tag" title="" class="post-tag" href="http://stackoverflow.com/questions/tagged/creative-commons">creative-commons</a> <a rel="tag" title="show questions tagged 'textbooks'" class="post-tag" href="http://stackoverflow.com/questions/tagged/textbooks">textbooks</a> 
        </div>
        <div class="started fr">
            <div class="user-info"><div class="user-details"><span title="This post is community owned as of Oct 12 '08 at 0:35. Votes do not generate reputation, and it can be edited by users with 100 rep" class="community-wiki">community wiki</span></div><br/><div class="user-details"><a title="show revision history for this post" href="http://stackoverflow.com/posts/194812/revisions" id="history-194812">106 revs, 71 users 12%<br/>xenoterracide</a></div></div>
        </div>  
    </div>
</div>