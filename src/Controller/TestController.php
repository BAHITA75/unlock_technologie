<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use App\Repository\ProgrammingLanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TestController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }

    /**
     * @Route("/test", name="test", methods={"GET", "POST"})
     */
    public function index(?Calendar $calendarr, CalendarRepository $calendary, Request $request, ProgrammingLanguageRepository $programe): Response
    {

        // $url = $request->query->get('donnees');
          
        // $urlStart = substr($url,10,24);
        // $urlEnd = substr($url,43,-2);

        // $test = array($urlStart,$urlEnd);
    

        // if(!isset($url)){

        // return $this->redirectToRoute('api_test');

        // }

        $category = $programe->findAll();

        $query = $this->entityManager->createQuery(
            'SELECT c
                FROM App:Calendar c
            WHERE c.title != :title
            ORDER BY c.title ASC'
        )->setParameter('title', 'indisponible');
        $calendar = $query->getResult();
        $calendars = new Calendar;
        $form = $this->createForm(CalendarType::class, $calendars);
        $calendrier = $calendary->findAll();
        $code="";//initialisation
     


        // if($request->isXmlHttpRequest()) {
        //     var_dump("ok");
        //     $test = $request->request->get('donnees');
        // }

       
        
        
        
        if (!empty($url)){



            // $routerName = $request->getRequestUri();

            // dd($routerName);

            //dd($t1);

            /** Je met ma requête ajax(GET) dans une variable :
             * Etant donnée qu'ell est en string() je délimite la bout concernant a START
             * Et le bout concernant END
             */
            $urlStart = substr($url,10,24);
            $urlEnd = substr($url,43,-2);
            $test = array($urlStart,$urlEnd);
            $start = new \DateTime($urlStart);
            $end = new \DateTime($urlEnd);
            $interval = DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod($start,$interval,$end);



            /**
             * L'ensemble de mes dates de la BDD
             * Je vais choisir des dates aléatoires à partir de la BDD
             */
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.start,c.end')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $maBddDates = $query->getResult();

            $dateInit = array_rand($maBddDates,1);
            $dateSqlInit = $maBddDates[$dateInit]['start'];
            $dateSqlInit->format('Y-m-d');
            $startDate = $dateSqlInit->format('Y-m-d H:i:s');

            $dateFin = array_rand($maBddDates,1);
            $dateSqlFin = $maBddDates[$dateFin]['end'];
            $dateSqlFin->format('Y-m-d');
            $endDate = $dateSqlFin->format('Y-m-d H:i:s');




            

            //Mon intervalle de date issue de la BDD

            //Debut interval
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c, MIN(c.start)')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $minStartBdd = $query->getResult();
            $DateS = $minStartBdd[0][1];
            $DateDebut = new \DateTime($DateS);
        

            //Fin interval
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c, MAX(c.end)')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $maxEndBdd = $query->getResult();
            $DateE = $maxEndBdd[0][1];
            $DateFin = new \DateTime($DateE);

        
            //Délimitation de l'interval à partir de DateDebut et DateFin
            $zoneDateBdd = DateInterval::createFromDateString('1 day');
            $intervalBdd = new DatePeriod($DateDebut,$zoneDateBdd,$DateFin);

        

        
            foreach($intervalBdd as $zoneDate){

                // echo $zoneDate->format('Y-m-d H:i:s').'<br>';

                //mettre mon interval de dates BDD dans un tableau

            $intervalBddNew[] = $zoneDate->format('Y-m-d H:i:s');
            }


                // dd($intervalBddNew);

                /**
                 * Verifier si les dates selectionnées dans le calendrier sont dans notre intevalle de dates BDD
                 */
                if(($start >= $intervalBddNew || $start <=  $intervalBddNew ) || ($end >= $intervalBddNew || $end <= $intervalBddNew)){

                        $code = 200;

                        // dd($intervalBddNew);


                            foreach($daterange as $newTest){
                    
                            
                                
                            $newTest->format('Y-m-d H:i:s');//J'inspecte les dates du select(daterange) et je m'assure de bien y être
                    
                            $dateTest[] = $newTest->format('Y-m-d H:i:s');

                            }
                    
                            $queryBuilder = $this->entityManager->createQueryBuilder();
                            $queryBuilder->select('c.start','c.end','c.teacher_name')
                            ->from(Calendar::class, 'c')
                            ->add('where', "c.start IN ( :date) OR c.end IN ( :date)")
                            ->setParameter('date', $dateTest);
                    
                            $query = $queryBuilder->getQuery();
                            $calendar1 = $query->getResult();

                            /**"array_rand" sélectionne une ou plusieurs valeurs au hasard dans un tableau et retourne la ou les clés de ces valeurs. 
                             * Cette fonction utilise un pseudo générateur de nombre aléatoire, 
                             * ce qui ne convient pas pour de la cryptographie. */

                            if(!empty($calendar1)){

                            $dateInit = array_rand($calendar1,1);
                            $dateSqlInit = $calendar1[$dateInit]['start'];
                            $dateSqlInit->format('Y-m-d');
                            $startDate = $dateSqlInit->format('Y-m-d H:i:s');

                            $dateFin = array_rand($calendar1,1);
                            $dateSqlFin = $calendar1[$dateFin]['end'];
                            $dateSqlFin->format('Y-m-d');
                            $endDate = $dateSqlFin->format('Y-m-d H:i:s');

                            echo $startDate;
                            echo '<br>';
                            echo $endDate;

                            }


                            // dd($date);

                            //($startDate >= $date || $startDate <=  $date ) || ($endDate >= $date || $end <= $date)
                            // if(in_array($startDate, $dateTest, false) || in_array($endDate, $dateTest, false)){

                            // echo 'Vous etes bien dans l\'interval';
                                    $code = 200;

                                    // dd($date);

                                        $queryBuilder = $this->entityManager->createQueryBuilder();
                                        $queryBuilder->select('c.teacher_name')
                                            ->from(Calendar::class, 'c')
                                            ->add('where', "c.start not IN ( :date) AND c.end not IN ( :date)")
                                            ->setParameter('date', $dateTest);

                                            $query = $queryBuilder->getQuery();

                                            $calendario = $query->getResult();

                                            // echo '<pre>'; print_r($calendario); echo '</pre>';

                                

                                            return $this->render('test/test.html.twig',[

                                                'calendar' => $calendar,
                                                'form'=> $form->createView(),
                                                'calendrier'=> $calendario,
                                                'category'=>$category,
                                                'code'=>$code,
                                            ]);

                }
                else
                {

                            $code = 201;


                            // echo '<pre>'; print_r($calendario); echo '</pre>';

                            return $this->render('test/test.html.twig',[

                                'calendar' => $calendar,
                                'form'=> $form->createView(),
                                'calendary'=> $calendrier,
                                'category'=>$category,
                                'code'=>$code,
                            ]);
                        

                }

         //------------------------------------------------TEST----------------------------------------------

        }
        else
        {


            
            


            $code = 201;

            return $this->render('test/test.html.twig',[

                'calendar' => $calendar,
                'form'=> $form->createView(),
                'code'=>$code,
                'calendary'=>$calendrier,
                'category'=>$category,
                
            ]);



            // $url = $t1;
            // $urlStart = substr($url,10,24);
            // $urlEnd = substr($url,43,-2);
            // $test = array($urlStart,$urlEnd);
          
            // $url2 = $this->generateUrl('test',$test);
            
           
            // dd($url2);


            // return $this->redirect($this->generateUrl('test',$test));




        }

        


        return $this->redirectToRoute('test');
        // unset($_GET['donnees']);
    
    }

    /**
     * @Route("/test/delete/{id}", name="test_delete")
     */
    public function delete(Calendar $calendar, CalendarRepository $calendars, EntityManagerInterface $em, Request $request, $id): Response
    {
    
        $donnees = json_decode($request->getContent());


        $calendar->getId();
        $user = $this->getUser(); 
        $id_user = $this->getUser('id');
       

        $calendars->findBy(['id'=>$id]);

        $em->remove($calendar);
        $em->flush();
        
       

        $calendar = $calendars->findAll();

        // dd($events);

      
    


        return $this->render('test/test.html.twig', [

            'calendar' => $calendar,
        ]);

    }



    /**
     * @Route("/test2", name="test2", methods={"GET", "POST"})
     */
    public function index2(?Calendar $calendarr, CalendarRepository $calendar, Request $request): Response
    {

         //On instancie une notification
        $calendarr = new Calendar;

        $request = json_decode($request->getContent());
        
        $url = $_GET['donnees'];
        $urlStart = substr($url,10,24);
        $urlEnd = substr($url,43,-2);
        // dd($donnees);
        // $request = Request::createFromGlobals();
        // $request->query->get('start');
        // $request->query->get('end');
        // $donnees = json_decode($request->getContent());
        // $calendarr->setStart(new \Datetime($donnees->start));
        // $calendarr->setEnd(new \DateTime($donnees->end));
        $start = new \DateTime($urlStart);
        $end = new \DateTime($urlEnd);
        $interval = DateInterval::createFromDateString('1 day');
        $daterange = new DatePeriod($start,$interval,$end);
        
       //$test = new \DateTime('2022-02-28 00:00:00');

    //    $monTest = $test->format('Y-m-d');
    //dd($end);

    if($daterange){

    foreach($daterange as $newTest){

        
            
        $newTest->format('Y-m-d H:i:s');

        $dateTest[] = $newTest->format('Y-m-d H:i:s');


        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.start','c.end','c.teacher_name')
        ->from(Calendar::class, 'c')
        ->add('where', "c.start IN ( :date) OR c.end IN ( :date)")
        ->setParameter('date', $dateTest);

        $query = $queryBuilder->getQuery();

        $calendar = $query->getResult();

    }

}else{


    $queryBuilder = $this->entityManager->createQueryBuilder();
    $queryBuilder->select('c.start','c.end','c.teacher_name')
    ->from(Calendar::class, 'c');

    $query = $queryBuilder->getQuery();

    $calendar = $query->getResult();








}
        
        /**"array_rand" sélectionne une ou plusieurs valeurs au hasard dans un tableau et retourne la ou les clés de ces valeurs. 
         * Cette fonction utilise un pseudo générateur de nombre aléatoire, 
         * ce qui ne convient pas pour de la cryptographie. */

        $dateInit = array_rand($calendar,1);
        $dateSqlInit = $calendar[$dateInit]['start'];
        $dateSqlInit->format('Y-m-d');
        $startDate = $dateSqlInit->format('Y-m-d H:i:s');

        $dateFin = array_rand($calendar,1);
        $dateSqlFin = $calendar[$dateFin]['end'];
        $dateSqlFin->format('Y-m-d');
        $endDate = $dateSqlFin->format('Y-m-d H:i:s');
    
    

        echo $startDate;
        echo '<br>';
        echo $endDate;
        
        echo '<br>';
        echo '<br>';


        // $input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
        // $rand_keys = array_rand($input, 2);
        // echo $input[$rand_keys[2]] . "\n";
        // echo $input[$rand_keys[1]] . "\n";

        $date[]="";

        foreach($daterange as $date2){

            echo $date2->format('Y-m-d H:i:s').'<br>';

            $date[] = $date2->format('Y-m-d H:i:s');
    
         }

        $date[] = $date2->format('Y-m-d H:i:s');

        //  echo '<pre>'; print_r($date); echo '</pre>';
         echo '<br>';
         echo '<br>';

        // echo '<pre>'; print_r($calendar); echo '</pre>';

       

         if(in_array($startDate, $date, false) || in_array($endDate, $date, false)){


            // $startDate = new DateTime(trim($startDate));
            // $startDate->format('Y-m-d H:i:s');
            // $endDate = new DateTime(trim($endDate));
            // $startDate->format('Y-m-d H:i:s');


            echo 'Vous etes bien dans l\'interval';
            

            // foreach($date as $newTest){

        
            
            // $newTest->format('Y-m-d H:i:s');

            // dd($newTest);

            // if($newTest == $startDate || $newTest == $endDate){

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
                ->from(Calendar::class, 'c')
                ->add('where', "c.start not IN ( :date) AND c.end not IN ( :date)")
                ->setParameter('date', $date);

                $query = $queryBuilder->getQuery();

                $calendario = $query->getResult();

                echo '<pre>'; print_r($calendario); echo '</pre>';

                // $startDate->modify('+1 week');
                // $endDate->modify('+1 week');


            

             

            

           

        }else{

            echo 'Vous n\'etes pas dans l\'interval';

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
            ->from(Calendar::class, 'c');

            $query = $queryBuilder->getQuery();

            $calendario = $query->getResult();


            echo '<pre>'; print_r($calendario); echo '</pre>';
        };
         

         // reverse the array
         
        //  $daterange = array_reverse(iterator_to_array($daterange));
                  
        //  foreach($daterange as $date1){
        //     echo $date1->format('Y-m-d').'<br>';
        //  }

     echo '<br>';
     echo '<br>';

        
// $input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
// $rand_keys = array_rand($input, 2);
// echo $input[$rand_keys[2]] . "\n";
// echo $input[$rand_keys[1]] . "\n";

            
            


        return $this->render('test/test2.html.twig');


    }

}