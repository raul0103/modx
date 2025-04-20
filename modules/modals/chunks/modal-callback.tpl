{extends "file:modules/modals/layots/base.tpl"} 

{block 'params'}
  {set $id='modal-callback'}
  {set $title="Заказать звонок"}
{/block}

{block 'body'}
  {set $email_subject = 'Сообщение со страницы '~$_modx->resource.pagetitle}
  {'!FetchIt' | snippet : [
    'form' => '@INLINE 
      <form class="form d-grid gap-16" data-modal-id="modal-callback">
        <div class="form__group">
          <label>Ваше имя</label>
          <input type="text" name="name"/>
        </div>

        <div class="form__group">
          <label>Телефон</label>
          <input type="tel" name="phone" placeholder="+7 (___) ___-__-__" required/>
          <span class="error-color fs-caption" data-error="phone" style="display: none;"></span>
        </div>

        <label class="custom-checkbox">
          <input type="checkbox" checked required/>
          <span class="checkmark"></span>
          Я согласен на обработку <a href="{$_modx->makeURL("policy_id"|config)}" class="btn-link">персональных данных</a>
        </label>

        <div class="form__footer">
          <button class="btn btn-primary big-btn w-100" type="submit">Отправить</button>
        </div>
      </form>
    '
    'emailTo' => 'email' | config
    'emailSubject' => $email_subject
    'emailTpl' => '@FILE chunks/fetchit-email-tpl.tpl'
    'hooks' => 'email'
    'snippet' => 'FormIt'
    'customValidators' => 'phone-format'
    'validate' => 'phone:required:phone-format'
  ]}
{/block}