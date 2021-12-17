<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 4/6/2021
 * Time: 5:25 PM
 */

namespace App\Command;


use App\Entity\Post;
use App\Service\Core\Mailer;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Acl\Exception\Exception;

class StatisticsTranslationCommand extends Command
{
    protected static $defaultName = 'app:get-translation-stats';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $mailer;

    public function __construct( EntityManagerInterface $entityManager,
                                 Mailer $mailer)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Send to email Stadistics Translation.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send and email with statidistics of translations made by users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $startDate = Carbon::now(); //returns current day
            $started_day = $startDate->firstOfMonth();
            $current_day =  Carbon::instance(new \DateTime('now'));
            $stadistics = $this->entityManager->getRepository(Post::class)->getTranslatedPostAtMonthCurrentDay($started_day ,$current_day);
            //dump($stadistics);
        }catch (Exception $exception){
            $output->writeln('Error to retrieve data from Posts: '. $exception->getMessage());
            return 1;
        }

        $output->writeln('Data from Youtube treeding generated!');
        $this->mailer->sendUpdateTranslationStadisticsEmail($stadistics);
        return 0;
    }

//    protected function updateYoutubeTreedingEntity($data = array(), $new_artist = array()){
//        $today = new \DateTime();
//        $sortArrayAfter = array();
//        $sortArrayBefore = array();
//        if(count($this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findAll()) > 0){
//            $sortArrayBefore = $this->reorderValues();
//        }
//        // UPDATE RECORD IF IS FOUND IN WEEK TREEDING
//        foreach ($data as $clave => $valor) {
//            $youtube_entity = $this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findOneByChannelId($valor['channel_id']);
//
//            if(!is_null($youtube_entity)){
//                $youtube_entity->setLasWeekPosition($youtube_entity->getCurrentWeekPosition());
//                $youtube_entity->setCurrentWeekPosition($clave);
//                $youtube_entity->setLikes($valor["statistics"]["likeCount"]);
//                $youtube_entity->setViews($valor["statistics"]["viewCount"]);
//                $youtube_entity->setDislikes($valor["statistics"]["dislikeCount"]);
//                $youtube_entity->setComments($valor["statistics"]["commentCount"]);
//                $this->entityManager->persist($youtube_entity);
//                $this->entityManager->flush();
//            }
//            // CREATE NEW RECORD FOR NEW ARTIST IN YOUTUBE TREEDING
//            foreach ($new_artist as $item) {
//                if($valor["channel_id"] == $item['channel_id']){
//                    $artist_entity = $this->entityManager->getRepository(Artist::class)->findOneById($item['id']);
//                    $youtube_entity_new = new \App\Entity\Profile\YoutubeTreeding();
//                    $youtube_entity_new->setArtist($artist_entity);
//                    $youtube_entity_new->setVideoId($valor["video_id"]);
//                    $youtube_entity_new->setChannelId($item['channel_id']);
//                    $youtube_entity_new->setLasWeekPosition(0);
//                    $youtube_entity_new->setCurrentWeekPosition($clave);
//                    $youtube_entity_new->setLikes($valor["statistics"]["likeCount"]);
//                    $youtube_entity_new->setViews($valor["statistics"]["viewCount"]);
//                    $youtube_entity_new->setDislikes($valor["statistics"]["dislikeCount"]);
//                    $youtube_entity_new->setComments($valor["statistics"]["commentCount"]);
//                    $youtube_entity_new->setVideoTitle($valor["title"]);
//                    $this->entityManager->persist($youtube_entity_new);
//                    $this->entityManager->flush();
//                }
//            }
//        }
//        // REMOVE RECORD IF IS NOT IN THE WEEK TREEDING
//        foreach ($this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findAll() as $item) {
//            $not_updated = $today->diff($item->getUpdated());
//            if($not_updated->days != 0) {
//                $this->entityManager->remove($item);
//                $this->entityManager->flush();
//            }
//        }
//        $sortArrayAfter = $this->reorderValues();
//        foreach ($sortArrayAfter as $index => $item) {
//            $record = $this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findOneById($item["id"]);
//            // incrementar/anular semanas de duracion en el top 10
//            if( $index +1 <= 10){
//                $record->setDurationSamePosition($record->getDurationSamePosition() +1);
//            }else{
//                $record->setDurationSamePosition(0);
//            }
//            //PEAK posicion mas alta que ha tenido en la APP propia
//            if( $record->getPeak() >= $index +1 ){
//                $record->setPeak($index +1);
//            }elseif ($record->getPeak() == 0){
//                $record->setPeak($index +1);
//            }
//            $this->entityManager->persist($record);
//            $this->entityManager->flush();
//        }
//        // LasWeek Position de acuerdo al orden en la APP propia
//        foreach ($sortArrayBefore as $index => $item) {
//            $record = $this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findOneById($item["id"]);
//            if($record){
//                $record->setLastWeekApp($index +1);
//                $this->entityManager->persist($record);
//                $this->entityManager->flush();
//            }
//        }
//    }
//
//    protected function reorderValues(){
//        $sorted = array();
//        $sortArray = array();
//        foreach ($this->entityManager->getRepository(\App\Entity\Profile\YoutubeTreeding::class)->findAll() as $key => $item) {
//            $sorted[$key]["id"] = $item->getId();
//            $sorted[$key]["currentWeekPosition"] = $item->getCurrentWeekPosition();
//            $sorted[$key]["lasWeekPosition"] = $item->getLasWeekPosition();
//        }
//        foreach($sorted as $item){
//            foreach($item as $key=>$value){
//                if(!isset($sortArray[$key])){
//                    $sortArray[$key] = array();
//                }
//                $sortArray[$key][] = $value;
//            }
//        }
//        $orderby = "currentWeekPosition"; //change this to whatever key you want from the array
//        if(count($sortArray) != null && count($sorted) != null){
//            array_multisort($sortArray[$orderby],SORT_ASC,$sorted);
//        }
//        // dump($sorted,$sortArray);die;
//        return $sorted;
//    }
}