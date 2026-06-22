import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'

interface Props {
    href: string
    label: string
    cost: number
    money: number
}

export function formatMoney(amount: number): string {
    if (amount >= 1_000_000) {
return `$${(amount / 1_000_000).toFixed(1)}M`
}

    if (amount >= 1_000) {
return `$${(amount / 1_000).toFixed(1)}k`
}

    return `$${amount.toFixed(0)}`
}

export default function UpgradeButton({ href, label, cost, money }: Props) {
    const canAfford = money >= cost

    return (
        <Button
            variant="outline"
            size="sm"
            disabled={!canAfford}
            className="w-full justify-between gap-2 text-xs"
            onClick={() => router.post(href)}
        >
            <span>⬆ {label}</span>
            <span className={canAfford ? 'text-green-600 dark:text-green-400' : 'text-red-500'}>
                {formatMoney(cost)}
            </span>
        </Button>
    )
}
