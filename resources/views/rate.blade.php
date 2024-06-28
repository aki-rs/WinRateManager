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
                                            <img src="{{ asset('images/1/${data.characterId}.PNG') }}" class="playerImg" alt="${data.characterName}">
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
            $(document).on('click', '#reset', function() {
                // var characterId = $('#character-select').val();
                if (characterId) {
                    $('#resetModal input[name="character_id"]').val(characterId);
            $('#resetModal .character-name').text(characterName);
                    $('#resetModal').show();
                }
            });

            // Modal cancel button click event
            $(document).on('click', '#cancel', function() {
                $('#resetModal').hide();
            });
        });
    </script>
</head>
<body>
    <section id="resetModal" class="modalBack" style="display: none;">
        <div>
            <div>
                <p><span class="character-name"></span>の戦績を全てリセットしますか？</p><br>
                <p>この操作は取り消しできません</p>
                <div class="flex">
                    <a href="" class="addButton flex">
                        <div class="Yellow h35 w115 flex">
                            <button method="Post" class="YellowContent h27 w107 flex">リセット</button>
                        </div>
                    </a>
                    <button class="addButton flex" id = "cancel">
                        <div class="Black h35 w115 flex">
                            <div class="BlackContent h27 w107 flex">キャンセル</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </section>
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
        <div class="flex mt10">
            <div id="resetButton"></div>
        </div>
        
    </main>
    <footer></footer>
</body>
</html>
