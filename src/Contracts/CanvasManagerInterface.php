<?php

namespace CanvasLMS\Laravel\Contracts;

interface CanvasManagerInterface
{
    /**
     * Switch to a different Canvas connection.
     *
     * @param string $name The connection name
     *
     * @throws \InvalidArgumentException
     */
    public function connection(string $name): self;

    /**
     * Get the current connection name.
     */
    public function getConnection(): string;

    /**
     * Get the configuration for the current connection.
     *
     * @return array<string, mixed>|null
     */
    public function getConnectionConfig(): ?array;

    /**
     * Get all available connection names.
     *
     * @return array<string>
     */
    public function getAvailableConnections(): array;

    /**
     * Execute a callback using a specific connection.
     *
     * @param string   $connection The connection name
     * @param callable $callback   The callback to execute
     */
    public function usingConnection(string $connection, callable $callback): mixed;
}
