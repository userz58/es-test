<?php

namespace App\Command;

use App\Model\Elastically\Category;
use App\Model\Elastically\Product;
use Elastica\Document;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use JoliCode\Elastically\Client;

#[AsCommand(
    name: 'app-test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        private Client $client,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $category1 = new Category('cat-a', 'a', 'A');
        $category2 = new Category('cat-b', 'a/b', 'B');
        $category3 = new Category('cat-c', 'c', 'C');

        $product1 = new Product('aaa', '123456', 'product 1', [$category1, $category2, $category3], ['power' => '10 кВт']);
        $product2 = new Product('bbb', '234567', 'product 2', [$category2, $category3], ['power' => '250 Вт']);
        $product3 = new Product('ccc', '887766', 'product 3', [$category2], ['power' => '30 кВт']);

        $indexBuilder = $this->client->getIndexBuilder();
        $newIndex = $indexBuilder->createIndex('product');
        $indexer = $this->client->getIndexer();

        $indexer->scheduleIndex($newIndex, new Document(1, $product1));
        $indexer->scheduleIndex($newIndex, new Document(2, $product2));
        $indexer->scheduleIndex($newIndex, new Document(3, $product3));
        $indexer->flush();

        $indexBuilder->markAsLive($newIndex, 'product');
        $indexBuilder->speedUpRefresh($newIndex);
        $indexBuilder->purgeOldIndices('product');

        // todo: ...
        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');


        return Command::SUCCESS;
    }
}
