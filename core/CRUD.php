<?php
/**
 * Класс для работы с БД
 * ВСЕ Методы CRUD
 * При создании возвращает общее число строк в таблице
 * Работает через подготовленные запросы
 */
// TODO Множественный LIKE_RAVNO array
namespace core;

use core\Db;


final class CRUD
{

    private $db;
    private $table;
    public  $TotalRows;
    public  $CurrentRows;
    public  $Resulting;

    public function __construct($table,$add=null) // Принимает имя таблицы с которой будет работать модель
    {
        $this->db = Db::init(); // Инициализиркет подключение к БД
        $this->table=$table; // Записываем в свойство имя таблицы
        $statement=$this->db->query("SELECT id FROM {$this->table} "); // Готовим запрос И узнаем сколько всего строк в таблице
        $this->TotalRows=$statement->rowCount(); // Считаем строки

    }

    /**
     * @param array|null $ColName
     * @param null $OR_AND
     * @param null $LIKE_RAVNO
     * @param null $needle
     * @param int $limit
     * @param int $offset
     *
     * @return CRUD
     */

    // Чтение SELECT

    public function GetInfo(array $ColName=null, $OR_AND=null, $LIKE_RAVNO=null,  $needle=null, $limit=null, $offset=0,$and = null) // Метод получения данных из таблицы
        /**
         * Массив ячеек таблицы в которых будет проводиться поиск если НЕТ то будет искать везде
         * Как будем искать И / ИЛИ (по умолчанию ИЛИ)
         * Четкость ТОЧНО / ПРИБЛИЗИТЕЛЬНО (По умолчанию ПРИБЛИЗИТЕЛЬНО)
         * ТО ЧТО БУДЕМ ИСКАТЬ
         * ЛИМИТ выборки (по умолчанию ВСЕ)
         * СДВИГ (по умолчанию 0)
         * Добавление произвольного значения
         */
    {
        unset($this->Results,$this->CurrentRows); // Очищаем свойства Текущий результат и Текущее количество строк
        if(is_null($limit)){$limit=$this->TotalRows;} // Если нет ограничения поиска то приминаем в его качестве общее количество строк в БД

        if((is_null($ColName))||(is_null($needle))) //  ЕСЛИ нет параметров поиска
        {
             $statement=$this->db->prepare("SELECT * FROM {$this->table} {$and} LIMIT :limit OFFSET :offset "); // Готовим запрос
             $statement->execute(array('limit'=>$limit,'offset'=>$offset)); // Выполняем запрос
             $this->CurrentRows=$statement->rowCount(); // Посчитали строки
             $this->Resulting=$statement->fetchAll(); // Записываем результат в массив



        }
        else // Если есть то что что мы ищем и где мы ищем
            {
                if($LIKE_RAVNO==='LIKE'){$needle="%".$needle."%";} // Преобразуем строку поиска для выражения LIKE
                if(is_null($OR_AND)){$OR_AND=' OR ';} // Если не пришел параметр то будем искать ИЛИ

                $req="SELECT * FROM `".$this->table."` WHERE ".$ColName[0]." ".$LIKE_RAVNO." :needle0 "; // Начальная часть запроса
                //$req2="SELECT * FROM `".$this->table."` WHERE ".$ColName[0]." ".$LIKE_RAVNO." :needle0 "; // Начальная часть запроса
                $req_params['needle0']=$needle; // Начало параметров
                //$req_params2['needle0']=$needle; // Начало параметров
                $iMax=count($ColName); // посчитали количество столбцов в которых будем искать

                    for($i=1; $i < $iMax; $i++) // создаем строку поиска
                        {
                            $req.=" ".$OR_AND." ".$ColName[$i]." ".$LIKE_RAVNO."  :needle".$i; // Строка запроса
                            $req_params['needle'.$i]=$needle;                                  // Массив параметров

                            /*$req2.=" ".$OR_AND." ".$ColName[$i]." ".$LIKE_RAVNO."  :needle".$i; // Строка запроса
                            $req_params2['needle'.$i]=$needle;  */                                // Массив параметров
                        }

                $req.=" LIMIT :limit OFFSET :offset"; // Добавляем к строке полученный результат CТРОКА ГОТОВА
                $req_params['limit']=$limit; //довавление параметра ограничения выборки
                $req_params['offset']=$offset; // добавление параметра сдвига выборки
                $statement=$this->db->prepare($req) or die($this->db->error); // Выполняем запрос к БД
                $statement->execute($req_params); // Выполняем запрос
                $this->CurrentRows=$statement->rowCount(); // Посчитали количество строк
                $this->Resulting=$statement->fetchAll(); // Записываем массив ответа


            }
        return $this; // Возвращаем результат
    }

    // Добавление INSERT
    public function Add(array $INSERT_ARRAY=null ) // Принимаем массив колонка=>значение
    {
        if (is_null($INSERT_ARRAY)) { return false;}//Возвращаем ощибку если  условие не проходит
        else{
            $cols= array_keys($INSERT_ARRAY); // Собираем ключи
            $col=$cols[0]; // пишем первый ключ (возможно что он и единственный)
            $val=":".$cols[0]; // пишем первое значение (возможно что оно и единственное)
            for ($i=1,$IMax=count($INSERT_ARRAY); $i < $IMax; $i++) // Перебираем остаток массива
            {
                $col.=", ".$cols[$i]; // Дописываем ключи
                $val.=", :".$cols[$i]; // Дописываем плейсхолдеры
            }

            $statment=$this->db->prepare("INSERT INTO ".$this->table." (".$col.") VALUES (".$val.")"); // Готовим выражение
            $statment->execute($INSERT_ARRAY); // Передаем данные
            return true;
        }

    }



    // Изменение UPDATE
    public function Update(array $TargetCals_and_SETTINGS = null,  $OR_AND='OR', $LIKE_RAVNO='LIKE', array $Search_Area=null)
    {
        // Готовим строку с конца к началу
        if($OR_AND==='LIKE') //  Если у нас не строгое соответствие в услових поиска то переворачиваем все значения массива Search_Area
        {
            $temp_keys=array_keys($Search_Area); // Получили ключи массива
            for ($i=0,$IMax=count($Search_Area); $i<$IMax; $i++) // Перебираем массив и забираем значения
            {
                $temp_vals[]="%".$Search_Area[$i]."%"; // Переделываем значение под параметр LIKE
            }
            unset($Search_Area); //  Очищаем массив SearchArea
            for($i=0,$Imax=count($temp_keys); $i < $IMax; $i++) // Собираем массив Search_Area
            {
                $Search_Area[$temp_keys[$i]]=$temp_vals[$i]; // Пишем в него ключи и значения
            }
            unset($temp_keys,$temp_vals); // Освобождаем память от временных массивов
        }
        if(is_null($TargetCals_and_SETTINGS)){return false;} // Если нечего обновлять то просто возвращаем false
        if(is_array($Search_Area)){if(count($Search_Area)<2){$OR_AND=null;}}// Проверяем количество условий и если условие только одно то можно уничтожить переменную OR_AND
        if(is_null($Search_Area)){$col=1;} // Если у нас нет места поиска ячеек то будем искать везде WHERE 1
        else
        {
                $cols=array_keys($Search_Area); // Забрали все ключи массива
                $col=$cols[0]; // Записали значение первого параметра
                $col.=" = :".$cols[0]; // Записали первый плэйсхолдер
                for($i=1,$IMax=count($Search_Area); $i < $IMax; $i++) // Перебираем остаток массива ячеек поиска
                {
                    $col.=" ".$OR_AND." ".$cols[$i]." ".$LIKE_RAVNO." :".$cols[$i];
                }

                $target_cols=array_keys($TargetCals_and_SETTINGS); // Начинаем работу с ячейками котоые нужно обновить ЗАБИРАЕМ имена ячеек
                $target_col=$target_cols[0]." = :".$target_cols[0]; // Пишем первую пару

                for ($i=1,$IMax=count($TargetCals_and_SETTINGS); $i < $IMax; $i++) // Перибираем остатки массива изменяемых ячеек
                {
                    $target_col.=", ".$target_cols[$i]." = :".$target_cols[$i];
                }
                $result=array_merge($TargetCals_and_SETTINGS,$Search_Area); // Объединяем массивы для передачи параметров

                $statement=$this->db->prepare("UPDATE ".$this->table." SET ".$target_col." WHERE ".$col); // Готовим запрос
                $statement->execute($result); // Выполняем запрос
            return $statement->rowCount(); // Возвращаем количство затронутых строк
        }

    }

    // Удаление DELETE
    public function Delete(array $ColName,$OR_AND=null,$LIKE_RAVNO='LIKE')
    {
            $cols=array_keys($ColName); // Забрали имена ячеек
            if($LIKE_RAVNO==='LIKE')// Если у нас похожие то перебираем массив ячеек и меняем в нем значения на %ЗНАЧЕНИЕ%
            {

                foreach ($ColName as $el) // Перебираем массив
                {
                    $temp_vals[]="%".$el."%"; // Записываем измененные значения в свой темповый массив
                }
                unset($ColName); // Уничтожаем страрый массив
                for($i=0,$Imax=count($cols); $i <$Imax; $i++)
                {
                    $ColName[$cols[$i]]=$temp_vals[$i]; // Перезаписываем массив ячеек и значений
                }
                unset($temp_vals); // Удаляем темповый массив
            }
            $col=$cols[0]." ".$LIKE_RAVNO." :".$cols[0]; // Пишем первое выражение
            for($i=1,$IMax=count($cols); $i < $IMax; $i++)
            {
                $col.=" ".$OR_AND." ".$cols[$i]." ".$LIKE_RAVNO." :".$cols[$i];
            }
            $statement=$this->db->prepare("DELETE FROM ".$this->table." WHERE ".$col);
            $statement->execute($ColName);
    }

    public function FreeInfo($str, $needle){
        $statement=$this->db->prepare($str);
        $statement->execute($needle);
        $this->CurrentRows=$statement->rowCount(); // Посчитали строки
        $this->Resulting=$statement->fetchAll();
        return $this;
    }

    // Конец Удаления
}