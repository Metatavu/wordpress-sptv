<?php

declare(strict_types = 1);

namespace Elasticsearch\Namespaces;

use Elasticsearch\Endpoints\Ingest\Pipeline\Delete;
use Elasticsearch\Endpoints\Ingest\Pipeline\Get;
use Elasticsearch\Endpoints\Ingest\Pipeline\ProcessorGrok;
use Elasticsearch\Endpoints\Ingest\Pipeline\Put;
use Elasticsearch\Endpoints\Ingest\Simulate;

/**
 * Class IngestNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\IngestNamespace
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class IngestNamespace extends AbstractNamespace
{
    /**
     * $params['master_timeout']             = (time) Explicit operation timeout for connection to master node
     *        ['timeout']                    = (time) Explicit operation timeout
     *
     * @return callable|array
     */
    public function deletePipeline(array $params = [])
    {
        $id = $this->extractArgument($params, 'id');

        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var Delete $endpoint
*/
        $endpoint = $endpointBuilder('Ingest\Pipeline\Delete');
        $endpoint->setID($id);
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }

    /**
     * $params['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *
     * @return callable|array
     */
    public function getPipeline(array $params = [])
    {
        $id = $this->extractArgument($params, 'id');

        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var Get $endpoint
*/
        $endpoint = $endpointBuilder('Ingest\Pipeline\Get');
        $endpoint->setID($id);
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }

    /**
     * $params['master_timeout']             = (time) Explicit operation timeout for connection to master node
     *        ['timeout']                    = (time) Explicit operation timeout
     *
     * @return callable|array
     */
    public function putPipeline(array $params = [])
    {
        $body = $this->extractArgument($params, 'body');
        $id = $this->extractArgument($params, 'id');

        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var Put $endpoint
*/
        $endpoint = $endpointBuilder('Ingest\Pipeline\Put');
        $endpoint->setID($id)
            ->setBody($body)
            ->setParams($params);

        return $this->performRequest($endpoint);
    }

    /**
     * $params['verbose'] = (bool) Verbose mode. Display data output for each processor in executed pipeline
     *
     * @return callable|array
     */
    public function simulate(array $params = [])
    {
        $body = $this->extractArgument($params, 'body');
        $id = $this->extractArgument($params, 'id');

        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var Simulate $endpoint
*/
        $endpoint = $endpointBuilder('Ingest\Simulate');
        $endpoint->setID($id)
            ->setBody($body)
            ->setParams($params);

        return $this->performRequest($endpoint);
    }

    /**
     * @return callable|array
     */
    public function processorGrok(array $params = [])
    {
        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var ProcessorGrok $endpoint
*/
        $endpoint = $endpointBuilder('Ingest\ProcessorGrok');

        return $this->performRequest($endpoint);
    }
}
