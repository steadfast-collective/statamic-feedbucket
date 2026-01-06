import { router } from '@statamic/cms/inertia';


Statamic.booted(() => {

    router.on('navigate', (event) => {

        onFeedbucketReady(feedbucket => {
            const show = event.detail.page.props.statamicFeedbucket?.show !== false;
            if(!show) {
                feedbucket.style.display = 'none'
            } else {
                feedbucket.style.display = 'inline'
            }
        })


    })

});
