<?php
use Livewire\Volt\Component;
use App\Models\FlashCard;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

new class extends Component implements HasForms, HasActions {
    use InteractsWithActions;
    use InteractsWithForms;

    public $cards;
    public function boot()
    {
        $unlearned_cards_count = FlashCard::where('status', 'unlearned')->count();
        if ($unlearned_cards_count < 4) {
            FlashCard::query()->update(['status' => 'unlearned']);
        }
    }

    public function mount()
    {
        $this->cards = FlashCard::where('status', 'unlearned')->inRandomOrder()->limit(4)->get();
        foreach ($this->cards as $card) {
            $card->status = 'learned';
            $card->save();
        }

        // dd($this->cards);
    }
}
?>
<div>
    {{-- DESCRIPTION --}}
    <div class="flashcard-description">
        <h2>Welcome to the Flashcard section!</h2>
        <p>Get ready to expand your knowledge with our dynamic flashcards</p>
        <p>Click on a card to reveal the answer</p>

    </div>

    {{-- FLASHCARDS --}}
    <div class="flashcard-grid">
        @foreach($cards as $card)
            <div class="flashcard">
                <div class="card">
                    <div class="card-face card-front">
                        <p>{{ $card->question }}</p>
                    </div>
                    <div class="card-face card-back">
                        <p>{{ $card->answer }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    
    {{-- CSS STYLE --}}
    <style>
        .flashcard-description {
            /* max-width: 1000px; */
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .flashcard-description h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .flashcard-description p {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .flashcard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 12px;
        }
    
        .flashcard {
            width: 100%;
            height: 200px;
            perspective: 1000px;
            cursor: pointer;
        }
    
        .card {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.5s;
        }
    
        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #333;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    
        .card-front {
            background-color: #fff;
            color: #333;
        }
    
        .card-back {
            background-color: #f0f0f0;
            color: #333;
            transform: rotateY(180deg);
        }
    </style>
    

    {{-- JS SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const cards = document.querySelectorAll(".card");
    
            cards.forEach(card => {
                card.addEventListener("click", function () {
                    this.style.transform = this.style.transform === "rotateY(180deg)" ? "rotateY(0deg)" : "rotateY(180deg)";
                });
            });
        });
    </script>
    
</div>