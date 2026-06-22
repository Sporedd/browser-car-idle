import { Head, usePoll } from '@inertiajs/react'
import GameController from '@/actions/App/Http/Controllers/GameController'
import FactoryPanel from '@/components/game/FactoryPanel'
import ResourcePanel from '@/components/game/ResourcePanel'
import ShowroomPanel from '@/components/game/ShowroomPanel'
import TransportPanel from '@/components/game/TransportPanel'
import { formatMoney } from '@/components/game/UpgradeButton'
import type { GameStateData } from '@/types/game'

interface Props {
    gameState: GameStateData
    activeTab: string
}

const panels: Record<string, (props: Props) => React.ReactNode> = {
    resources: ({ gameState }) => <ResourcePanel nodes={gameState.resourceNodes} money={gameState.money} />,
    factory: ({ gameState }) => (
        <FactoryPanel lines={gameState.factoryLines} nodes={gameState.resourceNodes} money={gameState.money} />
    ),
    transport: ({ gameState }) => (
        <TransportPanel
            vehicles={gameState.transportVehicles}
            lines={gameState.factoryLines}
            showrooms={gameState.showrooms}
            money={gameState.money}
        />
    ),
    showrooms: ({ gameState }) => <ShowroomPanel showrooms={gameState.showrooms} money={gameState.money} />,
}

export default function Game(props: Props) {
    const { gameState, activeTab } = props
    usePoll(5000, { only: ['gameState'] })

    const renderPanel = panels[activeTab] ?? panels.resources

    return (
        <>
            <Head title="Car Empire" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between rounded-xl border bg-card px-5 py-3 shadow-sm">
                    <div className="flex items-center gap-2">
                        <span className="text-xl">🚗</span>
                        <span className="text-lg font-bold tracking-tight">Car Empire</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="text-muted-foreground text-sm">Balance</span>
                        <span className="font-mono text-xl font-bold text-emerald-600 dark:text-emerald-400">
                            {formatMoney(gameState.money)}
                        </span>
                    </div>
                </div>

                {renderPanel(props)}
            </div>
        </>
    )
}

Game.layout = {
    breadcrumbs: [{ title: 'Car Empire', href: GameController().url }],
}
