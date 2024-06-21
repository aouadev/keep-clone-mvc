<div class="barre">
    <?php $back_page = "" ?>
    <?php if($back == "back_my_notes"): ?>
        <?php $back_page = "note/index" ?>
    <?php elseif($back == "back_archives"): ?>
        <?php $back_page = "user/my_archives" ?>
    <?php elseif (strpos($back, "back_shared_by_") === 0 ): ?>
        <?php $sharer_id = (int)str_replace("back_shared_by_", "", $back);?>
        <?php $back_page = "user/get_shared_by/$sharer_id" ?>
    <?php endif; ?>
    <?php if (strpos($back, "search_") === 0 ):?>
        <?php $encoded_data = str_replace("search_", "", $back); ?>
        <?php $back_page = "note/search/$encoded_data"; ?>

    <?php endif; ?>       
    <a class="back" href= <?=$back_page?>><span class="material-symbols-outlined">arrow_back_ios</span></a>
    <?php if ($archived == 1) : ?>
      <div classe="delete-archive" >  
            <form action="note/delete_note/<?= $note_id ?>" id="deleteForm"  method="post">
            <input type="hidden" name="note_id" id="noteIdInput">
                <button name="delete_note" class="delete" type="submit" id="delete_icon" value="<?= $note_id ?>"><span class="material-symbols-outlined">delete_forever</span></button>
                <a class="unarchive" href="open_note/unarchive/<?= $note_id ?>"><span class="material-symbols-outlined">unarchive</span></a>
            </form>
      </div>
      <noscript>
      <div classe="delete-archive" >  
            <form action="note/delete_note_php/<?= $note_id ?>" id="deleteForm"  method="post">
            <input type="hidden" name="note_id" id="noteIdInput">
                <button name="delete_note" class="delete" type="submit" id="delete_icon" value="<?= $note_id ?>"><span class="material-symbols-outlined">delete_forever</span></button>
                <a class="unarchive" href="open_note/unarchive/<?= $note_id ?>"><span class="material-symbols-outlined">unarchive</span></a>
            </form>
      </div>
      </noscript>
        


    <?php elseif ($isShared_as_editor == 1) : ?>
        <a class="labels" href="note/open_labels/<?=$note_id?>"><span class="material-symbols-outlined">label</span></a>
        <a class="isShared" href="open_note/edit/<?= $note_id ?>"><span class="material-symbols-outlined">edit</span></a>
    <?php elseif ($archived == 0 && $isShared_as_editor == 0 && $isShared_as_reader == 0) : ?>
        <a class="share" href="note/open_shares/<?= $note_id ?>"><span class="material-symbols-outlined">share</span></a>
        <?php if ($pinned) : ?>
            <a class="pinned" href="open_note/unpin/<?= $note_id ?>"><span class="material-symbols-rounded">push_pin</span>
            <?php else : ?>
                <a class="pinned" href="open_note/pin/<?= $note_id ?>"><span class="material-symbols-outlined">push_pin</span></a>
            <?php endif; ?>
            <a class="archive" href="open_note/archive/<?= $note_id ?>"><span class="material-symbols-outlined">archive</span></a>
            <a class="labels" href="note/open_labels/<?=$note_id?>"><span class="material-symbols-outlined">label</span></a>
            <a class="isShared" href=<?= $type == TypeNote::TN ? "note/edit_text_note/$note_id" : "note/edit_checklist_note/$note_id";?> ><span class="material-symbols-outlined">edit</span></a>
        <?php endif; ?>

</div>

<div class="dates"><?= $get_time?></div>
<div class="title_note_title">Title</div>
<div class="title_note"> <?= $note->title ?></div>