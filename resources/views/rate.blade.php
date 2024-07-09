<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@3.0.2/destyle.css">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var characterId;
            var characterName;
            $('#character-select').change(function() {
                characterId = $(this).val();
                characterName = $(this).find('option:selected').text();
                if (characterId) {
                    $.ajax({
                        url: "{{ route('calcWinRate') }}",
                        method: 'POST',
                        dataType: "json",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            character_id: characterId
                        },
                        success: function(data) {
                            /*==========================================================

                                キャラクター毎/ステージ毎の勝率表示部分

                            ==========================================================*/
                            var winRateHtml = `
                                <div class="winrate">
                                    <div class="title">
                                        <p class="item">勝率(味方)</p>
                                        <p class="item">勝率(相手)</p>
                                    </div>
                                    <ul>
                                        ${data.characterWinRates.map(rate => `
                                            <li class="posRate">
                                                <div class="titleImg">
                                                    <img src="{{ asset('images/1/${rate.id}.PNG') }}" class="titleImg" alt="${rate.name}">
                                                </div>
                                                <div class="YPos">
                                                    <p class="itemList">${rate.allyWinRate}</p>
                                                    <p class="itemListR">${rate.enemyWinRate}</p>
                                                </div>
                                            </li>
                                        `).join('')}
                                    </ul>
                                    <h4 class="">ステージ勝率</h4>
                                    <ul>
                                        ${data.stageWinRates.map(rate => `
                                            <li class="stageRate">
                                                <div class="stageImgPart">
                                                    <img src="{{ asset('images/stages/${rate.id}.PNG') }}" class="stageImg" alt="${rate.name}">
                                                </div>
                                                <div class="YPos">
                                                    <p class="stageItemList">${rate.stageWinRate}</p>
                                                </div>
                                            </li>
                                        `).join('')}
                                    </ul>
                                    <div class="dammy"></div>
                                </div>
                            `;

                            /*==========================================================

                                使用キャラクター勝率表示部分

                            ==========================================================*/
                            var playerRateHtml = `
                                <div class="playerBack">
                                    <div class="backPart">
                                        <div class="playerPart">
                                            <p class="letterWhite">${data.characterName}</p>
                                            <p class="showPRate">${data.playerWinRate}</p>
                                        </div>
                                        <div class="">
                                            <img src="{{ asset('images/1/${data.characterId}.PNG') }}" class="playerImg" alt="${data.characterName}">
                                        </div>
                                    </div>
                                </div>
                            `;

                            /*==========================================================

                                リセットボタン表示部分

                            ==========================================================*/
                            var resetButton =`
                                <button href="" class="addButton flex" id="reset">
                                    <div class="Black h36 w116 flex">
                                        <div class="BlackContent h28 w108 flex">リセット</div>
                                    </div>
                                </button>
                            `;
                            $('#character-win-rate').html(winRateHtml);
                            $('#player-win-rate').html(playerRateHtml);
                            $('#resetButton').html(resetButton);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error fetching win rates: ", textStatus, errorThrown);
                        }
                    });
                } else {
                    $('#character-win-rate').html('');
                    $('#player-win-rate').html('');
                    $('#resetButton').hetml('');
                }
            });

            //リセットボタン押下
            $(document).on('click', '#reset', function() {
                if (characterId) {
                    $('#resetModal input[name="character_id"]').val(characterId);//リセット用
                    $('#resetModal .character-name').text(characterName);//モーダル内キャラクター名表示用
                    $('#resetModal').show();
                    $('body').css('overflow', 'hidden'); 
                }
            });

            //キャンセルボタンクリックでモーダルキャンセル
            $(document).on('click', '#cancel', function() {
                $('#resetModal').hide();
                $('body').css('overflow', 'auto'); 
            });

            //モーダル背景クリックでキャンセル
            $(document).on('click', '.modalBack', function() {
                $('#resetModal').hide();
                $('body').css('overflow', 'auto');
            });

            //フォーム内クリックによるモーダルキャンセル防止
            $(document).on('click', '.modal', function(event) {
                event.stopPropagation();
            });

        });
    </script>
</head>
<body>
    <!--==========================================================

        モーダルウィンドウ

    ==========================================================-->
    <form action="{{ route('resetMatch') }}" id="resetModal" class="modalBack"  method="POST" style="display: none;" autocomplete="off">
        @csrf
        <div class="w415">
            <div class="modalConfirm flexStart">

                <!-- 確認アイコン -->
                <img src="{{ asset('images/confirm.PNG') }}" alt="" class="confirmImg">
                <p class="headerConfirm letterWhite">確認</p>
            </div>
            <div class="modal w415">

                <!-- 確認表示 -->
                <input type="" name="character_id" style="display: none;" value="">
                <p class="character-name"></p>
                <p>上記キャラクターの戦績を全てリセットしますか？</p>
                <p>この操作は取り消しできません</p>

                <!-- ボタン -->
                <div class="mt10 flex">
                    <button type="submit" class="addButton flex">
                        <div class="Yellow h36 w116 flex">
                            <div class="YellowContent h28 w108 flex">リセット</div>
                        </div>
                    </button>
                    <a class="addButton flex" id = "cancel">
                        <div class="Black h36 w116 flex">
                            <div class="BlackContent h28 w108 flex">キャンセル</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!--==========================================================

        ヘッダー

    ==========================================================-->
    <header class="head">
        <div class="header_content">

            <!-- 表示キャラクター選択 -->
            <div class="chooser flex">
                <div class="Black h36 w415 flex">
                    <div class="BlackContent h28 w407 fLeft chooserSize-wrapper">
                        <select id="character-select" class="chooserSize" autocomplete="off">
                            <option value="" disabled selected>-</option>
                            @foreach ($characters as $character)
                                <option value="{{ $character->id }}">{{ $character->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- ボタン -->
            <a href="{{ route('moveToRegisterMatch') }}" class="addButton flex">
                <div class="Yellow h36 w116 flex">
                    <div class="YellowContent h28 w108 flex">戦績登録</div>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="LogOut flex">
                @csrf
                <button type="submit" class="Black h36 w116 flex">
                    <div class="BlackContent h28 w108 flex">ログアウト</div>
                </button>
            </form>
        </div>
    </header>

    <!--==========================================================

        勝率表示部分

    ==========================================================-->
    <main>
        <section id="player-win-rate"></section>
        <div class="back">
            <section class="winrate back" id="character-win-rate"></section>
        </div>
        <div class="flex mt10">
            <div id="resetButton"></div>
        </div>
        
    </main>
    <footer></footer>
</body>
</html>
