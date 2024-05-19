<div class="barre">
    <?php if($back == "back_my_notes"): ?>
        <?php $back_page = "note/index" ?>
    <?php elseif($back == "back_archives"): ?>
        <?php $back_page = "user/my_archives" ?>
    <?php elseif ($back == "back_shared_by"):?>
        <?php $back_page= "user/get_shared_by/$param3" ?> 

    <?php endif; ?>       
    <a class="back" href= <?=$back_page?>><span class="material-symbols-outlined">arrow_back_ios</span></a>
    <?php if ($archived == 1) : ?>

        <form action="note/delete_note" id="deleteForm" method="post">
            <button name="delete_note" class="delete" type="submit" id="delete_icon" value="<?= $note_id ?>"><span class="material-symbols-outlined">delete_forever</span></button>
        </form>
        <a class="unarchive" href="open_note/unarchive/<?= $note_id ?>"><span class="material-symbols-outlined">unarchive</span></a>


    <?php elseif ($isShared_as_editor == 1) : ?>
        <a class="isShared" href="open_note/edit/<?= $note_id ?>"><span class="material-symbols-outlined">edit</span></a>
    <?php elseif ($archived == 0 && $isShared_as_editor == 0 && $isShared_as_reader == 0) : ?>
        <a class="share" href="note/open_shares/<?= $note_id ?>"><span class="material-symbols-outlined">share</span></a>
        <?php if ($pinned) : ?>
            <a class="pinned" href="open_note/unpin/<?= $note_id ?>"><span class="material-symbols-rounded">push_pin</span>
            <?php else : ?>
                <a class="pinned" href="open_note/pin/<?= $note_id ?>"><span class="material-symbols-outlined">push_pin</span></a>
            <?php endif; ?>
            <a class="archive" href="open_note/archive/<?= $note_id ?>"><span class="material-symbols-outlined">archive</span></a>
            <a class="isShared" href="open_note/edit/<?= $note_id ?>"><span class="material-symbols-outlined">edit</span></a>
        <?php endif; ?>

</div>

<div class="dates"><?= $get_time?></div>
<div class="title_note_title">Title</div>
<div class="title_note"> <?= $note->title ?></div>