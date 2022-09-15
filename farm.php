<?php
class Farm{
    
    protected $day = 0;
    protected $animals = [];
    protected $animalAcounts = []; // учёт животный
    protected $dailyProductAcounts = []; // Учёт продукции за день
    protected $totalProductAcounts = []; // Учёт продукции
    protected $weekEnd = false;

    public function __construct(){
        echo "\n[Farm simulator]\n==================\n\n";
    }

    public function run(){
        echo "[День " . $this->day ."]\n";

        if($this->weekEnd){
            $this->resetTotalProductAcount();
            $this->weekEnd = false;
        }        
        
        if($this->day == 0){
            for ($i = 0; $i < 10; $i++){
                $cow = new Cow(count($this->animals)); 
                $this->addAnimal($cow);
            }
            for ($i = 0; $i < 20; $i++){
                $chicken = new Chicken(count($this->animals));
                $this->addAnimal($chicken);
            }

            $this->showAnimalAcounts();
        }
        if($this->day > 0){
            if($this->day == 8){
                for ($i = 0; $i < 1; $i++){
                    $cow = new Cow(count($this->animals)); 
                    $this->addAnimal($cow);
                    $this->pointer++;
                }
                for ($i = 0; $i < 5; $i++){
                    $chicken = new Chicken(count($this->animals));
                    $this->addAnimal($chicken);
                    $this->pointer++;
                }
                $this->showAnimalAcounts();
            }
            $this->harvest();
            $this->showDailyProductAcounts();
            
            if ($this->day % 7 == 0){                
                $this->showTotalProductAcounts();
                $this->weekEnd = true;
            }
        }
        $this->day++;
        echo "________________\n";
    }
    
    protected function addAnimal(Animal $animal){
        $this->animals[] = $animal;
        $tmp = $animal->type;

        if (is_null($this->animalAcounts)){ // Если массив ещё пуст
            $this->animalAcounts[$tmp] = new AnimalAcount(1);
        }             
        if(array_key_exists($tmp, $this->animalAcounts)) {   // Если такой тип животного уже есть в массиве
            $this->animalAcounts[$tmp]->animalNumber++;
        }
        else{
            $this->animalAcounts[$tmp] = new AnimalAcount(1);
        }

        if (is_null($this->dailyProductAcounts)){ // Если массив ещё пуст
            $this->dailyProductAcounts[$tmp] = new ProductAcount($animal->productName, 0, $animal->unitName);
        } 
        if(!array_key_exists($tmp, $this->dailyProductAcounts)){
            $this->dailyProductAcounts[$tmp] = new ProductAcount($animal->productName, 0, $animal->unitName);
        }

        if (is_null($this->dailyProductAcounts)){ // Если массив ещё пуст
            $this->totalProductAcounts[$tmp] = new ProductAcount($animal->productName, 0, $animal->unitName);
        } 
        if(!array_key_exists($tmp, $this->totalProductAcounts)){
            $this->totalProductAcounts[$tmp] = new ProductAcount($animal->productName, 0, $animal->unitName);
        }
    }

    protected function harvest(){ // Сбор урожая
        $this->resetDailyProductAcount();
        for ($i=0; $i < count($this->animals); $i++){
            $tmp = $this->animals[$i]->harvest();
            $this->dailyProductAcounts[$this->animals[$i]->type]->productNumber += $tmp;
            $this->totalProductAcounts[$this->animals[$i]->type]->productNumber += $tmp;
        }
    }

    protected function resetDailyProductAcount(){ // сброс учёта
        $keys = array_keys($this->dailyProductAcounts);
        foreach ($keys as $k) {
            $this->dailyProductAcounts[$k]->productNumber = 0;
        }
    }

    protected function resetTotalProductAcount(){ // сброс учёта
        $keys = array_keys($this->totalProductAcounts);
        foreach ($keys as $k) {
            $this->totalProductAcounts[$k]->productNumber = 0;
        }
    }

    protected function showAnimalAcounts(){
        echo "Животных на ферме:\n";
        $keys = array_keys($this->animalAcounts);
        foreach ($keys as $k) {
            echo $k . ": " . $this->animalAcounts[$k]->animalNumber . " шт.\n";
        }
    } 
    
    protected function showDailyProductAcounts(){
        echo "Продукции за текущий день:\n";
        $keys = array_keys($this->dailyProductAcounts);
        foreach ($keys as $k) {
            echo $k . ": " . $this->dailyProductAcounts[$k]->productName . " в количестве " . $this->dailyProductAcounts[$k]->productNumber . " " . $this->dailyProductAcounts[$k]->unitName . "\n";
        }
    } 

    protected function showTotalProductAcounts(){
        echo "Всего продукции:\n";
        $keys = array_keys($this->totalProductAcounts);
        foreach ($keys as $k) {
            echo $k . ": " . $this->totalProductAcounts[$k]->productName . " в количестве " . $this->totalProductAcounts[$k]->productNumber . " " . $this->totalProductAcounts[$k]->unitName . "\n";
        }
    }
}

abstract class Animal{
    public $id = 0; // Индивидуальный номер
    public $type = "default"; // вид животного
    public $productName = "default"; // Название производимого ресурса
    public $unitName = "default"; // Единица измерения производимого ресурса

    public function __construct(int $id){
        $this->id = $id;
    }

    abstract public function harvest();
}

class Cow extends Animal{

    public function __construct(int $id){
        parent::__construct($id);
        $this->type = "корова";
        $this->productName = "молоко";
        $this->unitName = "л.";
    }

    public function harvest(){
        return rand(8, 12);
    }
}

class Chicken extends Animal{

    public function __construct(int $id){
        parent::__construct($id);
        $this->type = "курица";
        $this->productName = "яйцо";
        $this->unitName = "шт.";
    }

    public function harvest(){
        return rand(0, 1);
    }
}

class AnimalAcount{ // Учёт животных
    public $animalNumber = 0; // Число животных

    public function __construct(int $animalNumber){
        $this->animalNumber  = $animalNumber;
    }
}

class ProductAcount{ // Учёт продукции
    public $productNumber = 0; // Количество продукции
    public $productName = "default"; // Наименование продукции
    public $unitName = "default"; // Единица измерения

    public function __construct(string $productName, int $productNumber, string $unitName)
    {
        $this->productName = $productName ;
        $this->productNumber = $productNumber;
        $this->unitName = $unitName;
    }
}