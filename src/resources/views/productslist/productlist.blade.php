@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/contactlist.css') }}">
@endsection

@section('content')
<div class="contactlist__content">
  <div class="contactlist__heading">
    <h2>お問い合わせ一覧</h2>
  </div>
    
    <!-- 検索フォーム -->
    <form class="form" action="/contactlist/search" method="get">  
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <input type="text" name="email" value="{{ session('reset') ? '' : old('email', request('email')) }}" placeholder="メールアドレス入力" />
          </div>
        </div>
      </div>
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--select">
            <select name="gender">
              <option value="">性別</option>
              <option value="1" {{ session('reset') ? '' : (old('gender', request('gender')) == '1' ? 'selected' : '') }}>男性</option>
              <option value="2" {{ session('reset') ? '' : (old('gender', request('gender')) == '2' ? 'selected' : '') }}>女性</option>
              <option value="3" {{ session('reset') ? '' : (old('gender', request('gender')) == '3' ? 'selected' : '') }}>その他</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form__group">
        <div class="form__group-content">
            <div class="form__input--select">
              <select name="category_id">
                <option value="">お問い合わせ内容の種類を選択</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" {{ session('reset') ? '' : (old('category_id', request('category_id')) == $category->id ? 'selected' : '') }}>{{ $category->content }}</option>
                @endforeach
              </select>
            </div>
        </div>
      </div>
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--date">
            <input type="date" name="created_at" value="{{ session('reset') ? '' : old('created_at', request('created_at')) }}" id="date-picker">
            <input type="hidden" name="formatted_date" id="formatted-date" value="{{ session('reset') ? '' : old('formatted_date', request('formatted_date')) }}">
          </div>
        </div>
      </div>
      <!-- 検索ボタンとリセットボタン -->
      <div class="search-form__button">
        <button class="search-form__button-submit" type="submit">検索</button>
        <button class="reset-form__button-submit" type="button" onclick="resetForm()">リセット</button>
      </div>
    </form>
    
    <!-- ページネーションとエクスポートボタンを同じ行に配置 -->
    <div class="pagination-export-container">
      <div class="pagination">
        {{ $contacts->links('vendor.pagination.simple-bootstrap-4') }}
      </div>
      
      @if(isset($contacts) && count($contacts) > 0)
        <div class="export-form">
          <form class="export-form__button" action="/contactlist/export" method="get">
            @if(request('email'))
              <input type="hidden" name="email" value="{{ request('email') }}">
            @endif
            @if(request('gender'))
              <input type="hidden" name="gender" value="{{ request('gender') }}">
            @endif
            @if(request('category_id'))
              <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            @if(request('created_at'))
              <input type="hidden" name="created_at" value="{{ request('created_at') }}">
            @endif
            <button class="export-form__button-submit" type="submit">CSVエクスポート</button>
          </form>
        </div>
      @endif
    </div>

    @if(isset($contacts) && count($contacts) > 0)
      <!-- お問い合わせ一覧テーブル -->
      <div class="contactlist-table">
        <table class="contactlist-table__inner">
          <!-- テーブルヘッダー -->
          <tr class="contactlist-table__row">
            <th class="contactlist-table__header">お名前</th>
            <th class="contactlist-table__header">性別</th>
            <th class="contactlist-table__header">お問い合わせ内容の種類</th>
            <th class="contactlist-table__header"></th>
          </tr>
          <!-- お問い合わせアイテムの繰り返し表示 -->
          @foreach ($contacts as $contact)
            <tr class="contactlist-table__row">
              <!-- お問い合わせ内容とカテゴリ表示エリア -->
              <td class="contactlist-table__item">
                <!-- 更新フォーム -->
                <div class="update-form__item">
                  <!-- カテゴリ表示 -->
                  <p class="update-form__item-p">{{ $contact->last_name }} {{ $contact->first_name }}</p>
                </div>
              </td>
              <td class="contactlist-table__item">
                <!-- 更新フォーム -->
                <div class="update-form__item">
                  <!-- カテゴリ表示 -->
                  <p class="update-form__item-p">
                    @if($contact->gender == 1)
                      男性
                    @elseif($contact->gender == 2)
                      女性
                    @elseif($contact->gender == 3)
                      その他
                    @endif
                  </p>
                </div>
              </td>
              <td class="contactlist-table__item">
                <!-- 更新フォーム -->
                <div class="update-form__item">
                  <!-- カテゴリ表示 -->
                  <p class="update-form__item-p">{{$contact->category_name()}}</p>
                </div>
              </td>
              <!-- 詳細ボタンエリア -->
              <td class="contactlist-table__item">
                <div class="update-form__button">
                  <button class="openModal" 
                    data-contact-id="{{ $contact->id }}" 
                    data-contact-name="{{ $contact->last_name }} {{ $contact->first_name }}" 
                    data-contact-gender="{{ $contact->gender }}" 
                    data-contact-email="{{ $contact->email }}" 
                    data-contact-tel="{{ $contact->tell }}" 
                    data-contact-address="{{ $contact->address }}" 
                    data-contact-building="{{ $contact->building }}" 
                    data-contact-category="{{ $contact->category_name() }}" 
                    data-contact-detail="{{ $contact->detail }}">
                    詳細
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </table>
      </div>
    @endif
</div>

  <!-- モーダル本体 -->
  <div class="modal-overlay" id="modal">
    <div class="modal-content">
      <!-- ×ボタン -->
      <button class="modal-close-btn" id="modalCloseBtn">&times;</button>
      
      <h3>お問い合わせ詳細</h3>
      <div class="modal-details">
        <p><strong>お名前：</strong><span id="modal-name"></span></p>
        <p><strong>性別：</strong><span id="modal-gender"></span></p>
        <p><strong>メールアドレス：</strong><span id="modal-email"></span></p>
        <p><strong>電話番号：</strong><span id="modal-tel"></span></p>
        <p><strong>住所：</strong><span id="modal-address"></span></p>
        <p><strong>建物名：</strong><span id="modal-building"></span></p>
        <p><strong>お問い合わせ内容の種類：</strong><span id="modal-category"></span></p>
        <p><strong>お問い合わせ内容：</strong><span id="modal-detail"></span></p>
      </div>
      
      <!-- 下段のボタンエリア -->
      <div class="modal-buttons">
        <button class="modal-delete-btn" id="modalDeleteBtn">削除</button>
      </div>
    </div>
  </div>

<script>
    // フォームリセット関数
    function resetForm() {
        // フォームの入力値をクリア
        document.querySelector('input[name="email"]').value = '';
        document.querySelector('select[name="gender"]').value = '';
        document.querySelector('select[name="category_id"]').value = '';
        document.querySelector('input[name="created_at"]').value = '';
        document.querySelector('input[name="formatted_date"]').value = '';
        
        // フォームをリセットエンドポイントに送信
        fetch('/contactlist/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            // ページをリロードしてリセット状態を反映
            window.location.href = '/contactlist';
        });
    }

    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalDeleteBtn = document.getElementById('modalDeleteBtn');
    const modal = document.getElementById('modal');

    // 詳細ボタンのクリックイベント
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('openModal')) {
        // データ属性から情報を取得
        const name = e.target.getAttribute('data-contact-name');
        const gender = e.target.getAttribute('data-contact-gender');
        const email = e.target.getAttribute('data-contact-email');
        const tel = e.target.getAttribute('data-contact-tel');
        const address = e.target.getAttribute('data-contact-address');
        const building = e.target.getAttribute('data-contact-building');
        const category = e.target.getAttribute('data-contact-category');
        const detail = e.target.getAttribute('data-contact-detail');

        // 性別の表示を変換
        let genderText = '';
        if (gender == 1) {
          genderText = '男性';
        } else if (gender == 2) {
          genderText = '女性';
        } else if (gender == 3) {
          genderText = 'その他';
        }

        // モーダルに情報を表示
        document.getElementById('modal-name').textContent = name;
        document.getElementById('modal-gender').textContent = genderText;
        document.getElementById('modal-email').textContent = email;
        document.getElementById('modal-tel').textContent = tel;
        document.getElementById('modal-address').textContent = address;
        document.getElementById('modal-building').textContent = building || '未入力';
        document.getElementById('modal-category').textContent = category;
        document.getElementById('modal-detail').textContent = detail;

        // 削除ボタンにcontact_idを設定
        const contactId = e.target.getAttribute('data-contact-id');
        modalDeleteBtn.setAttribute('data-contact-id', contactId);

        // モーダルを表示
        modal.style.display = 'flex';
      }
    });

    // 右上×ボタンのイベント
    modalCloseBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    // 削除ボタンのイベント
    modalDeleteBtn.addEventListener('click', () => {
      if (confirm('このお問い合わせを削除しますか？')) {
        const contactId = modalDeleteBtn.getAttribute('data-contact-id');
        // 削除処理を実行
        fetch(`/contactlist/delete/${contactId}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(response => {
          if (response.ok) {
            // 削除成功時はページをリロード
            window.location.reload();
          } else {
            alert('削除に失敗しました。');
          }
        }).catch(error => {
          console.error('Error:', error);
          alert('削除に失敗しました。');
        });
      }
    });

    // 背景クリックでも閉じる
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
</script>
@endsection