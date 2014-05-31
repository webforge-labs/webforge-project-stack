<?php

namespace Webforge\ProjectStack\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FixturesCompilerPass implements CompilerPassInterface  {

  public function process(ContainerBuilder $container) {
    $definition = $container->getDefinition('projectstack.fixtures.parts_manager');

    $taggedServices = $container->findTaggedServiceIds('projectstack.partsfixture');

    foreach ($taggedServices as $id => $attributes) {
      $definition->addMethodCall(
        'setPartsFixture',
        array($attributes[0]['fixtureName'], new Reference($id))
      );
    }
  }
}