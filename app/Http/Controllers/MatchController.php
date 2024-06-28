<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Character;
use App\Models\Stage;
use App\Models\MatchCharacter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class MatchController extends Controller
{
    //登録フォーム遷移
    public function moveToRegisterMatch(){
        $characters = Character::all();
        $stages = Stage::all();
        return view('registerMatch',['characters' => $characters,'stages' => $stages]);
    }

    //試合登録用関数
    public function registerMatchFanc(Request $request){
        $validator = Validator::make($request->all(), [
            'your_character' => ['required', 'exists:characters,id'],
            'stage' => ['required', 'exists:stages,id'],
            'result' => ['required', 'in:win,lose'],
            'ally_characters' => ['required', 'array', 'size:2'],
            'ally_characters.*' => ['exists:characters,id'],
            'enemy_characters' => ['required', 'array', 'size:3'],
            'enemy_characters.*' => ['exists:characters,id'],
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        //試合データ作成
        $match = GameMatch::create([
            'user_id' => Auth::id(),
            'stage_id' => $request->stage,
            'result' => $request->result,
        ]);

        //自分のキャラクターを保存
        MatchCharacter::create([
            'match_id' => $match->id,
            'character_id' => $request->your_character,
            'role' => 'player',
        ]);

        //味方のキャラクターを保存
        foreach($request->ally_characters as $character_id){
            MatchCharacter::create([
                'match_id' => $match->id,
                'character_id' => $character_id,
                'role' => 'ally',
            ]);
        }

        //敵のキャラクターを保存
        foreach($request->enemy_characters as $character_id){
            MatchCharacter::create([
                'match_id' => $match->id,
                'character_id' => $character_id,
                'role' => 'enemy',
            ]);
        }

        return redirect()->route('moveToRate')->with('status', '正常に保存されました。');
    }

    //勝率表示画面遷移
    public function moveToRate(){
        $matches = GameMatch::where('user_id', Auth::id())->with('stage', 'matchCharacters.character')->get();
        $characters = Character::all();
        $stages = Stage::all();

        return view('rate', [
            'matches' => $matches,
            'characters' => $characters,
            'stages' => $stages
        ]);
    }

    //勝率計算用関数
    public function calcWinRate(Request $request){
        $choosen_character = $request->character_id;
        $character = Character::find($choosen_character);
        $matches = GameMatch::where('user_id', Auth::id())->with('matchCharacters.character')->get();
    
        $record = ['player' => ['win' => 0, 'lose' => 0], 'ally' => [], 'enemy' => [], 'stage' => []];
    
        foreach($matches as $match){
            $player = $match->matchCharacters->where('role', 'player')->first();
            if($player && $player->character_id == $choosen_character){
                if($match->result == 'win'){
                    $record['player']['win']++;
                }
                else{
                    $record['player']['lose']++;
                }
    
                foreach($match->matchCharacters as $matchCharacter){
                    if($matchCharacter->role == 'ally'){
                        if(!isset($record['ally'][$matchCharacter->character_id])){
                            $record['ally'][$matchCharacter->character_id] = ['win' => 0, 'lose' => 0];
                        }
    
                        if($match->result == 'win'){
                            $record['ally'][$matchCharacter->character_id]['win']++;
                        }
                        if($match->result == 'lose'){
                            $record['ally'][$matchCharacter->character_id]['lose']++;
                        }
    
                    }
                    if($matchCharacter->role == 'enemy'){
                        if(!isset($record['enemy'][$matchCharacter->character_id])){
                            $record['enemy'][$matchCharacter->character_id] = ['win' => 0, 'lose' => 0];
                        }
                        if($match->result == 'win'){
                            $record['enemy'][$matchCharacter->character_id]['win']++;
                        }
                        if($match->result == 'lose'){
                            $record['enemy'][$matchCharacter->character_id]['lose']++;
                        }
                    }
                }
    
                if(!isset($record['stage'][$match->stage_id])){
                    $record['stage'][$match->stage_id] = ['win' => 0, 'lose' => 0];
                }
                if($match->result == 'win'){
                    $record['stage'][$match->stage_id]['win']++;
                }
                if($match->result == 'lose'){
                    $record['stage'][$match->stage_id]['lose']++;
                }
            }
        }
    
        //自分のキャラクターの勝率
        $playerTotal = $record['player']['win'] + $record['player']['lose'];
        $playerRate = '-';
        if($playerTotal != 0){
            $playerRate = ($record['player']['win'] / $playerTotal) * 100; // playerの勝率
        }
    
        $allCharacters = Character::all();

        // 味方と敵の勝率
        $winRate = [];
    
        foreach($allCharacters as $all){
            $id = $all->id;
            $allyWinRate = '-';
            $enemyWinRate = '-';
    
            if(isset($record['ally'][$id])){
                $allyTotal = $record['ally'][$id]['win'] + $record['ally'][$id]['lose'];
                $allyWinRate = ($record['ally'][$id]['win'] / $allyTotal) * 100;
            }
            if(isset($record['enemy'][$id])){
                $enemyTotal = $record['enemy'][$id]['win'] + $record['enemy'][$id]['lose'];
                $enemyWinRate = ($record['enemy'][$id]['win'] / $enemyTotal) * 100;
            }
    
            $winRate[] = [
                'id' => $id,
                'name' => $all->name,
                'allyWinRate' => $allyWinRate !== '-' ? number_format($allyWinRate, 2) : '-',
                'enemyWinRate' => $enemyWinRate !== '-' ? number_format($enemyWinRate, 2) : '-',
            ];
        }
    
        //ステージの勝率
        $stageWinRate = [];
        $allStages = Stage::all();
    
        foreach($allStages as $stage){
            $st_id = $stage->id;
            $stageWin = '-';
            if(isset($record['stage'][$st_id])){
                $stageTotal = $record['stage'][$st_id]['win'] + $record['stage'][$st_id]['lose'];
                $stageWin = ($record['stage'][$st_id]['win'] / $stageTotal) * 100;
            }
    
            $stageWinRate[] = [
                'name' => $stage->name,
                'stageWinRate' => $stageWin !== '-' ? number_format($stageWin, 2) : '-',
            ];
        }
    
        return response()->json([
            'playerWinRate' => $playerRate !== '-' ? number_format($playerRate, 2) : '-',
            'characterWinRates' => $winRate,
            'stageWinRates' => $stageWinRate,
            'characterName' => $character->name,
            'characterId' => $character->id
        ]);
    }

    //試合リセット用関数
    // public function resetMatch(Request $request){
    //     $matchcharacter = MatchCharacter::find($request->character_id);
    //     $matchcharacter->delete();
    //     $gamematch = GameMatch::find(Auth::id(),$request->match_id);
    //     $gamematch->delete();
    //     return redirect()->route('moveToRate');
    // }


    //戦績リセット用関数
    // public function resetMatch(Request $request){
    //     $user = Auth::user();
    
    //     $gameMatch = GameMatch::where('id', $request->match_id)
    //                     ->where('user_id', $user->id)
    //                     ->first();
    
    //     if ($gameMatch) {
    //         $matchCharacter = MatchCharacter::where('id', $request->character_id)
    //                     ->where('match_id', $gameMatch->id)
    //                     ->first();

    //         if ($matchCharacter) {
    //             $matchCharacter->delete();
    //         }

    //         $gameMatch->delete();
    //     }
    
    //     return redirect()->route('moveToRate');
    // }

    public function resetMatch(Request $request){
        // Log::info('Reset match called with character_id: ' . $request->character_id);
        // $allMatches = GameMatch::all();
        // foreach ($allMatches as $match) {
        //     Log::info('GameMatch ID: ' . $match->id . ', User ID: ' . $match->user_id);
        // }
    
        $user = Auth::user();
    
        // ユーザーに関連する全ての試合を取得
        $gameMatches = GameMatch::where('user_id', $user->id)->get();
        if ($gameMatches->isEmpty()) {
            Log::info('No game matches found for user id: ' . $user->id);
        }
        
    
        foreach($gameMatches as $gameMatch){
    
            // 指定されたキャラクターIDとroleがselfの試合キャラクターを取得
            $matchCharacters = MatchCharacter::where('match_id', $gameMatch->id)
                                    ->where('character_id', $request->character_id)
                                    ->where('role', "player")
                                    ->get();

            if ($matchCharacters->isEmpty()) {
                Log::info('error');
            }
    
            // それぞれの試合キャラクターに対して操作
            foreach($matchCharacters as $matchCharacter){
                Log::info('Processing match character with id: ' . $matchCharacter->id);
    
                // 同じ試合IDの全ての試合キャラクターを取得
                $deleteMatchCharacters = MatchCharacter::where('match_id', $matchCharacter->match_id)->get();
                if($deleteMatchCharacters){
                    // 同じ試合IDのゲームマッチを取得
                    $deleteGameMatch = GameMatch::where('id', $matchCharacter->match_id)->first();
                    // 全ての試合キャラクターを削除
                    foreach ($deleteMatchCharacters as $deleteMatchCharacter) {
                        $deleteMatchCharacter->delete();
                    }
                    // 試合自体を削除
                    if($deleteGameMatch){
                        $deleteGameMatch->delete();
                    }
                }
            }
        }
    
        return redirect()->route('moveToRate');
    }
    
    
    
}
