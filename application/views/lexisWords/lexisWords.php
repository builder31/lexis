<?
wp_title('Dictionary');
get_header();?> 
<div id="container">
<p align="center">Define life in your language! <br />
  <?
  print $links."<br />";
  echo $words[0]->count_all(); ?> definitions added since January 2011.
  </p>
  <?php foreach ($words as $listedWord):
    $this->db->where($listedWord->getIdxCol(),$listedWord->getWordId());
    $comments = $this->db->get('oneword_comments')->result(); 
    ?>
    <div class="post">
      <h2 class="entry-title" style="text-transform:capitalize;margin:0px;padding:0px;"><?=anchor(base_url().$this->appURI.'view/'.$listedWord->getWordId(),$listedWord->getWord())?></h2>
      <div class="entry-meta"><?=date("M j, Y",strtotime($listedWord->getPosted()))?></div>
    <blockquote><?=$listedWord->getMeaning() ?></blockquote>
    <div class="entry-meta">
      Translated by <i><?=substr($listedWord->getAuthorId(),0,stripos($listedWord->getAuthorId(),'@'))?></i> from
      <?= anchor(base_url().$this->appURI.'dictionary/'.$listedWord->getLangId(),$listedWord->getLanguage()); ?><br />
      [<?=anchor(base_url().$this->appURI.'/view/'.$listedWord->getWordId().'#comments',count($comments)." comments");?>]
      [<?=anchor('','Share');?>]  </div>
      </div>
      <? endforeach; ?>
    </div>
<?
$this->load->view($this->appURI.'lexis_sidebar');
get_footer(); ?>