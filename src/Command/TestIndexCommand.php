<?php

namespace App\Command;

use App\Builder\Search\ProductModelBuilder;
use App\Model\Elastically\Category;
use App\Model\Elastically\Product;
use App\Repository\CatalogProductRepository;
use Elastica\Document;
use Elastica\Query\MultiMatch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use JoliCode\Elastically\Client;

#[AsCommand(
    name: 'app-test-index',
    description: 'Create index',
)]
class TestIndexCommand extends Command
{
    public function __construct(
        private Client                   $client,
        private CatalogProductRepository $productRepository,
        private ProductModelBuilder      $modelBuilder,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$indexName = sprintf('products-%s', date("Ymd"));
        $indexName = 'product';

        $indexBuilder = $this->client->getIndexBuilder();
        $index = $indexBuilder->createIndex($indexName);
        $indexer = $this->client->getIndexer();

        $products = $this->productRepository->iterareAll();
        foreach ($products as $product) {
            $model = $this->modelBuilder->create($product);
            $indexer->scheduleIndex($index, new Document($product->getId(), $model));
        }

        $indexer->flush();

        $indexBuilder->markAsLive($index, $indexName);
        $indexBuilder->speedUpRefresh($index);
        $indexBuilder->purgeOldIndices($indexName);

        // todo: ...
        $io->success('Index created');
        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
