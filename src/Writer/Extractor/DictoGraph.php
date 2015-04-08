<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Writer\Extractor;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph as FhacultyGraph;
use Fhaculty\Graph\Vertex;

class DictoGraph implements ExtractionInterface
{
    /** @var array */
    private $data;

    public function extract(FhacultyGraph $graph)
    {
        $this->data = array(
            'edges'    => array(),
            'vertices' => array(),
        );

        $edges = $graph->getEdges();
        foreach ($edges as $edge) {
            /** @var Directed $edge */
            $this->addEdge($edge);
        }

        $vertices = $graph->getVertices();
        foreach($vertices as $vertex) {
            $this->addVertex($vertex);
        }

        ksort($this->data['edges']);
        ksort($this->data['vertices']);

        return $this->data;
    }

    /**
     * @param Directed $edge
     */
    private function addEdge(Directed $edge)
    {
        $from = $edge->getVertexStart()->getId();
        $to = $edge->getVertexEnd()->getId();

        if(!array_key_exists($from, $this->data['edges'])) {
            $this->data['edges'][$from] = array();
        }

        if(!in_array($to, $this->data['edges'][$from])) {
            $this->data['edges'][$from][$to] = $this->extractEdge($edge);
        }
    }

    /**
     * @param Vertex $vertex
     */
    private function addVertex(Vertex $vertex)
    {
        $extracted =  $vertex->getId();
        if (!in_array($extracted, $this->data['vertices'])) {
            $this->data['vertices'][] = $extracted;
        }
    }

    /**
     * @param Directed $edge
     * @return array
     */
    private function extractEdge(Directed $edge)
    {
        return
            $this->extractEntities($edge->getAttribute('locations', array()));
    }

    /**
     * @param \PhpDA\Entity\Location[] | \PhpDA\Entity\Cycle[] $entities
     * @return array
     */
    private function extractEntities(array $entities)
    {
        $data = array();
        foreach ($entities as $entity) {
            $data[] = $entity->toArray();
        }

        return $data;
    }
}
