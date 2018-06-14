<?php
use Iresults\RepIo\Configuration\ConfigurationProvider;
use Iresults\RepIo\Enum\State as StateEnum;
use Iresults\RepIo\Model\Export\Sap\Processor;
use Iresults\RepIo\Model\Export\Sap\ProcessorConfiguration;
use Iresults\RepIo\Model\Export\State\State as StateObject;
use Iresults\RepIo\Model\Export\State\StateRepository;
use Iresults\RepIo\Model\Serialize\Csv\CsvBuilder;
use Iresults\RepIo\Model\Serialize\Formatter;
use Iresults\RepIo\Model\Serialize\Sap\OrderSerializer;
use Iresults\RepIo\Rfc\Http\Client as HttpClient;
use Iresults\RepIo\Rfc\Soap\ClientFactory as SoapClientFactory;
use Iresults\Shell\Command\AbstractCommand;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

class Iresults_Gdpr_Model_Shell_Command_Customer_Show extends AbstractCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        // TODO: Implement execute() method.
    }

    public static function getName()
    {
    return 'customer:show';
    }

}
