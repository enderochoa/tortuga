
  <div>

    <h2>DataObject - advanced functionalities</h2>
    To invoke a callback "before" and "after" a  save() or a delete() you can use:<br/>
    <strong>pre_process</strong> and <strong>post_process</strong> methods.<br/><br/>
    
    - <em>pre_process</em> can be used to halt execution (when the callback function return FALSE).<br/>
    - <em>post_process</em> is useful to do others stuff after action execution.<br/>
    
    <br/>
    In this sample, a DataObject is used to create an "article".<br />
    Then, the post_process function <em>relate_article_one()</em> is called.<br/>
    The function relate new inserted article with article 1.<br/>
    <br/>
    DataObject delete the new article, but before..<br/>
    the pre_process function <em>remove_rel_toany_article()</em> remove all relations (of other articles) with it.<br/>
    <br/>
    <div class="note">
    <?php echo $content?> 
    </div>
  </div>
