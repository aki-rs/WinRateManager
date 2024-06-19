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
            $('#character-select').change(function() {
                var characterId = $(this).val();
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
                                                    <img src="{{ asset('images/2/${rate.id}.PNG') }}" class="titleImg" alt="${rate.name}">
                                                </div>
                                                <div class="YPos">
                                                    <p class="itemList">${rate.allyWinRate}</p>
                                                    <p class="itemListR">${rate.enemyWinRate}</p>
                                                </div>
                                            </li>
                                        `).join('')}
                                    </ul>
                                    <h4>ステージ勝率</h4>
                                    <ul>
                                        ${data.stageWinRates.map(rate => `
                                            <li>${rate.name}: ${rate.stageWinRate}</li>
                                        `).join('')}
                                    </ul>
                                </div>
                            `;
                            var playerRateHtml = `
                                <div class="playerBack">
                                    <div class="backPart">
                                        <div class="playerPart">
                                            <p class="letterWhite">${data.characterName}</p>
                                            <p class="showPRate">${data.playerWinRate}</p>
                                        </div>
                                        <div class="">
                                            <img src="{{ asset('images/2/${data.characterId}.PNG') }}" class="playerImg" alt="${data.characterName}">
                                        </div>
                                    </div>
                                </div>
                            `;
                            var resetButton =`
                                <button href="" class="addButton flex" id="reset">
                                    <div class="Black h35 w115 flex">
                                        <div class="BlackContent h27 w107 flex">リセット</div>
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
        });
    </script>
</head>
<body>
    <!-- <section id="resetModal" class="modalBack">
        <div>
            <div>
                <p>このキャラクターの戦績を削除しますか？</p>
                <div class="flex">
                    <a href="" class="addButton flex">
                        <div class="Yellow h35 w115 flex">
                            <div class="YellowContent h27 w107 flex">削除</div>
                        </div>
                    </a>
                    <a href="" class="addButton flex">
                        <div class="Black h35 w115 flex">
                            <div class="BlackContent h27 w107 flex">キャンセル</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section> -->
    <header class="head">
        <div class="header_content">
            <div class="chooser flex">
                <div class="Black h35 w415 flex">
                    <div class="BlackContent h27 w407 fLeft chooserSize-wrapper">
                        <select id="character-select" class="chooserSize">
                            <option value="" disabled selected>-</option>
                            @foreach ($characters as $character)
                                <option value="{{ $character->id }}">{{ $character->name }}</option>
                            @endforeach
                            <p>▼</p>
                        </select>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('moveToRegisterMatch') }}" class="addButton flex">
                <div class="Yellow h35 w115 flex">
                    <div class="YellowContent h27 w107 flex">戦績登録</div>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="LogOut flex">
                @csrf
                <button type="submit" class="Black h35 w115 flex">
                    <div class="BlackContent h27 w107 flex">ログアウト</div>
                </button>
            </form>
        </div>
    </header>
    <main>
        <section id="player-win-rate"></section>
        <div class="back">
            <section class="winrate back" id="character-win-rate"></section>
        </div>
        <div class="flex">
            <div id="resetButton"></div>
        </div>
        
    </main>
    <footer></footer>
</body>
</html>
