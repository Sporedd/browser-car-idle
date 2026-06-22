import { router } from '@inertiajs/react'
import { useEffect, useReducer } from 'react'
import * as TransportController from '@/actions/App/Http/Controllers/TransportController'
import * as UpgradeController from '@/actions/App/Http/Controllers/UpgradeController'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { FactoryLineData, ShowroomData, TransportVehicleData } from '@/types/game'
import UpgradeButton from './UpgradeButton'

interface Props {
    vehicles: TransportVehicleData[]
    lines: FactoryLineData[]
    showrooms: ShowroomData[]
    money: number
}

const statusColors: Record<string, string> = {
    idle: 'bg-secondary text-secondary-foreground',
    in_transit: 'bg-blue-500/20 text-blue-700 dark:text-blue-400',
    returning: 'bg-amber-500/20 text-amber-700 dark:text-amber-400',
}

function formatRemaining(target: string | null): string {
    if (!target) {
return ''
}

    const diff = new Date(target).getTime() - Date.now()

    if (diff <= 0) {
return 'arriving...'
}

    const seconds = Math.ceil(diff / 1000)

    return seconds >= 60 ? `${Math.floor(seconds / 60)}m ${seconds % 60}s` : `${seconds}s`
}

function useCountdown(target: string | null): string {
    const [, tick] = useReducer((n: number) => n + 1, 0)

    useEffect(() => {
        if (!target) {
return
}

        const interval = setInterval(tick, 1000)

        return () => clearInterval(interval)
    }, [target])

    return formatRemaining(target)
}

function Vehicle({
    vehicle,
    lines,
    showrooms,
    money,
}: {
    vehicle: TransportVehicleData
    lines: FactoryLineData[]
    showrooms: ShowroomData[]
    money: number
}) {
    const arrivalCountdown = useCountdown(vehicle.arrivesAt)
    const returnCountdown = useCountdown(vehicle.returnsAt)
    const assignedShowroom = showrooms.find((s) => s.id === vehicle.assignedShowroomId)
    const totalReady = lines.reduce((sum, l) => sum + l.completedBuffer, 0)
    const canDispatch = vehicle.status === 'idle' && totalReady > 0

    const toLoad = Math.min(totalReady, vehicle.capacity)

    return (
        <div className="flex flex-col gap-3">
            <div className="flex items-center justify-between">
                <span className="font-medium">{vehicle.label}</span>
                <Badge className={statusColors[vehicle.status]} variant="outline">
                    {vehicle.status.replace('_', ' ')}
                </Badge>
            </div>

            <div className="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
                <div>
                    <span>Cargo: </span>
                    <span className="font-mono text-foreground">
                        {vehicle.cargoCount}/{vehicle.capacity}
                    </span>
                    {vehicle.cargoModel && (
                        <span className="ml-1 text-muted-foreground">({vehicle.cargoModel})</span>
                    )}
                </div>
                <div>
                    <span>Dest: </span>
                    <span className="text-foreground">{assignedShowroom?.name ?? '—'}</span>
                </div>
            </div>

            {vehicle.status === 'in_transit' && (
                <div className="flex items-center justify-between rounded-md bg-blue-500/10 px-3 py-2 text-xs text-blue-700 dark:text-blue-400">
                    <span>In transit</span>
                    <span className="font-mono">{arrivalCountdown}</span>
                </div>
            )}

            {vehicle.status === 'returning' && (
                <div className="flex items-center justify-between rounded-md bg-amber-500/10 px-3 py-2 text-xs text-amber-700 dark:text-amber-400">
                    <span>Returning empty</span>
                    <span className="font-mono">{returnCountdown}</span>
                </div>
            )}

            {vehicle.status === 'idle' && (
                <div className="flex items-center justify-between rounded-md bg-secondary px-3 py-2 text-xs text-muted-foreground">
                    <span>Ready to dispatch</span>
                    <span className="font-mono">{totalReady} cars available</span>
                </div>
            )}

            <Button
                size="sm"
                className="w-full text-xs"
                disabled={!canDispatch}
                onClick={() => router.post(TransportController.dispatch(vehicle.id).url)}
            >
                {canDispatch ? `Dispatch (${toLoad} cars)` : 'Dispatch'}
            </Button>

            <div className="flex flex-col gap-1.5">
                <UpgradeButton
                    href={UpgradeController.transportCapacity(vehicle.id).url}
                    label={`Capacity (Lv${vehicle.capacityLevel} → ${vehicle.capacityLevel + 1})`}
                    cost={vehicle.upgradeCapacityCost}
                    money={money}
                />
                <UpgradeButton
                    href={UpgradeController.transportSpeed(vehicle.id).url}
                    label={`Speed (Lv${vehicle.speedLevel} → ${vehicle.speedLevel + 1})`}
                    cost={vehicle.upgradeSpeedCost}
                    money={money}
                />
            </div>
        </div>
    )
}

export default function TransportPanel({ vehicles, lines, showrooms, money }: Props) {
    return (
        <Card className="flex flex-col gap-4 py-4">
            <CardHeader className="px-4 pb-0">
                <CardTitle className="flex items-center gap-2 text-base">
                    <span>🚛</span> Transport
                </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-4 px-4">
                {vehicles.map((v) => (
                    <Vehicle key={v.id} vehicle={v} lines={lines} showrooms={showrooms} money={money} />
                ))}
            </CardContent>
        </Card>
    )
}
