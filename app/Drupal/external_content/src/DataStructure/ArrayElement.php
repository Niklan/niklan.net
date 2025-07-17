<?php

namespace Drupal\external_content\DataStructure;

final class ArrayElement {

  /**
   * @var list<self>
   */
  private array $children = [];

  public function __construct(
    public readonly string $type,
    public readonly array $properties = [],
  ) {}

  public static function fromArray(array $data): self {
    $element = new self(
      $data['type'],
      $data['properties'] ?? [],
    );

    foreach ($data['children'] ?? [] as $child) {
      $element->addChild(self::fromArray($child));
    }

    return $element;
  }

  public function addChild(self $element): void {
    $this->children[] = $element;
  }

  /**
   * @return list<self>
   */
  public function getChildren(): array {
    return $this->children;
  }

  public function toArray(): array {
    $data = [
      'type' => $this->type,
      'properties' => $this->properties,
    ];

    if (\count($this->children) !== 0) {
      $data['children'] = \array_map(
        static fn (self $child) => $child->toArray(),
        $this->children,
      );
    }

    return $data;
  }

}
