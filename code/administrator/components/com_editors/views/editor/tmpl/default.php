<style src="media://com_editors/codemirror/css/docs.css" />

<?= @template('default_script') ?>
<div data-editor="<?= $name ?>">
<textarea id="<?= $name ?>" name="<?= $name ?>" cols="75" rows"25" class="editable validate-editor"><?= $data ?></textarea>
</div>